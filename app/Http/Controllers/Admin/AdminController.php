<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use App\Services\AdminPermissionRegistry;
use App\Services\AdminPermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminController extends BaseApiController
{
    public function __construct(
        private readonly AdminPermissionRegistry $registry,
        private readonly AdminPermissionService $permissions,
    ) {}

    private function authorizeOrFail(Request $request, string $permission): ?JsonResponse
    {
        $authenticatedAdmin = $request->user('api-admin');

        if (! $authenticatedAdmin instanceof Admin || ! $this->permissions->can($authenticatedAdmin, $permission)) {
            return $this->errorResponse('Forbidden', 403);
        }

        return null;
    }

    public function index(Request $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-admins')) {
            return $error;
        }

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'role' => [
                'nullable',
                'string',
                Rule::exists('roles', 'name')->where('guard_name', $this->registry->guard()),
            ],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $admins = Admin::query()
            ->with(['roles' => fn ($query) => $query
                ->where('guard_name', $this->registry->guard())
                ->orderBy('name')])
            ->when($validated['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('name', 'like', '%'.$search.'%')
                        ->orWhere('username', 'like', '%'.$search.'%');
                });
            })
            ->when($validated['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($validated['role'] ?? null, function ($query, string $role): void {
                $query->whereHas('roles', fn ($roles) => $roles
                    ->where('name', $role)
                    ->where('guard_name', $this->registry->guard()));
            })
            ->latest()
            ->paginate($validated['per_page'] ?? 15)
            ->withQueryString();

        return $this->successResponse(
            array_merge(
                ['admins' => AdminResource::collection($admins)->resolve()],
                $this->paginationMeta($admins)
            ),
            'Admins retrieved successfully'
        );
    }

    public function store(Request $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'create-admins')) {
            return $error;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('admins', 'username')],
            'password' => ['required', 'string', 'min:8'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'roles' => ['nullable', 'array'],
            'roles.*' => [
                'required',
                'string',
                'distinct',
                Rule::exists('roles', 'name')->where('guard_name', $this->registry->guard()),
            ],
        ]);

        /** @var Admin $authenticatedAdmin */
        $authenticatedAdmin = $request->user('api-admin');
        $roles = $validated['roles'] ?? [];

        if ($error = $this->authorizeSuperAdminRoleAssignment($authenticatedAdmin, $roles)) {
            return $error;
        }

        $admin = DB::transaction(function () use ($validated, $roles): Admin {
            $admin = Admin::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'password' => $validated['password'],
                'status' => $validated['status'] ?? 'active',
            ]);

            $admin->syncRoles($roles);

            return $admin;
        });

        return $this->successResponse(
            ['admin' => (new AdminResource($admin->load('roles')))->resolve()],
            'Admin created successfully',
            201
        );
    }

    public function show(Request $request, Admin $admin): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-admins')) {
            return $error;
        }

        return $this->successResponse(
            ['admin' => (new AdminResource($admin->load('roles')))->resolve()],
            'Admin retrieved successfully'
        );
    }

    public function update(Request $request, Admin $admin): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'update-admins')) {
            return $error;
        }

        /** @var Admin $authenticatedAdmin */
        $authenticatedAdmin = $request->user('api-admin');

        if ($this->isSuperAdmin($admin) && ! $this->permissions->isSuperAdmin($authenticatedAdmin)) {
            return $this->errorResponse('Only a SuperAdmin can update another SuperAdmin', 403);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'username' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('admins', 'username')->ignore($admin->id),
            ],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
            'roles' => ['sometimes', 'nullable', 'array'],
            'roles.*' => [
                'required',
                'string',
                'distinct',
                Rule::exists('roles', 'name')->where('guard_name', $this->registry->guard()),
            ],
        ]);

        $roles = array_key_exists('roles', $validated) ? ($validated['roles'] ?? []) : null;

        if ($roles !== null && ($error = $this->authorizeSuperAdminRoleAssignment($authenticatedAdmin, $roles))) {
            return $error;
        }

        $willRemoveSuperAdmin = $this->isSuperAdmin($admin)
            && $roles !== null
            && ! $this->containsSuperAdminRole($roles);
        $willDeactivate = $admin->status === 'active'
            && ($validated['status'] ?? $admin->status) === 'inactive';

        if (($willRemoveSuperAdmin || $willDeactivate) && $this->isLastActiveSuperAdmin($admin)) {
            return $this->errorResponse('The last active SuperAdmin cannot be deactivated or demoted', 422);
        }

        $attributes = collect($validated)
            ->only(['name', 'username', 'status'])
            ->all();

        if (isset($validated['password'])) {
            $attributes['password'] = $validated['password'];
        }

        DB::transaction(function () use ($admin, $attributes, $roles): void {
            $admin->update($attributes);

            if ($roles !== null) {
                $admin->syncRoles($roles);
            }

            if (($attributes['status'] ?? null) === 'inactive') {
                $admin->tokens()->delete();
            }
        });

        return $this->successResponse(
            ['admin' => (new AdminResource($admin->refresh()->load('roles')))->resolve()],
            'Admin updated successfully'
        );
    }

    public function destroy(Request $request, Admin $admin): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'delete-admins')) {
            return $error;
        }

        /** @var Admin $authenticatedAdmin */
        $authenticatedAdmin = $request->user('api-admin');

        if ($authenticatedAdmin->is($admin)) {
            return $this->errorResponse('You cannot delete your own admin account', 422);
        }

        if ($this->isSuperAdmin($admin) && ! $this->permissions->isSuperAdmin($authenticatedAdmin)) {
            return $this->errorResponse('Only a SuperAdmin can delete another SuperAdmin', 403);
        }

        if ($this->isLastActiveSuperAdmin($admin)) {
            return $this->errorResponse('The last active SuperAdmin cannot be deleted', 422);
        }

        DB::transaction(function () use ($admin): void {
            $admin->tokens()->delete();
            $admin->delete();
        });

        return $this->successResponse(null, 'Admin deleted successfully');
    }

    /**
     * @param  list<string>  $roles
     */
    private function authorizeSuperAdminRoleAssignment(Admin $authenticatedAdmin, array $roles): ?JsonResponse
    {
        if ($this->containsSuperAdminRole($roles) && ! $this->permissions->isSuperAdmin($authenticatedAdmin)) {
            return $this->errorResponse('Only a SuperAdmin can assign the SuperAdmin role', 403);
        }

        return null;
    }

    /**
     * @param  list<string>  $roles
     */
    private function containsSuperAdminRole(array $roles): bool
    {
        return collect($roles)->contains(
            fn (string $role): bool => strcasecmp($role, 'SuperAdmin') === 0
        );
    }

    private function isSuperAdmin(Admin $admin): bool
    {
        return $admin->roles()
            ->where('guard_name', $this->registry->guard())
            ->whereRaw('LOWER(name) = ?', ['superadmin'])
            ->exists();
    }

    private function isLastActiveSuperAdmin(Admin $admin): bool
    {
        if ($admin->status !== 'active' || ! $this->isSuperAdmin($admin)) {
            return false;
        }

        return Admin::query()
            ->where('status', 'active')
            ->whereHas('roles', fn ($query) => $query
                ->where('guard_name', $this->registry->guard())
                ->where('status', 'active')
                ->whereRaw('LOWER(name) = ?', ['superadmin']))
            ->count() === 1;
    }
}
