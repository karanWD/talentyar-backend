<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Admin;
use App\Models\Role;
use App\Services\AdminPermissionRegistry;
use App\Services\AdminPermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminRoleController extends BaseApiController
{
    public function __construct(
        private readonly AdminPermissionRegistry $registry,
        private readonly AdminPermissionService $permissions,
    ) {}

    private function authorizeOrFail(Request $request, string $permission): ?JsonResponse
    {
        $admin = $request->user('api-admin');
        if (! $admin instanceof Admin || ! $this->permissions->can($admin, $permission)) {
            return $this->errorResponse('Forbidden', 403);
        }

        return null;
    }

    public function index(Request $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-roles')) {
            return $error;
        }

        $roles = Role::query()
            ->where('guard_name', $this->registry->guard())
            ->with(['permissions:id,name', 'activityFields:id,name,slug,tag'])
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse(
            array_merge(
                ['roles' => $roles->items()],
                $this->paginationMeta($roles)
            ),
            'Roles retrieved successfully'
        );
    }

    public function store(Request $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'create-roles')) {
            return $error;
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where('guard_name', $this->registry->guard()),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'string',
                'distinct',
                Rule::exists('permissions', 'name')->where('guard_name', $this->registry->guard()),
            ],
            'activity_field_ids' => ['required', 'array', 'min:1'],
            'activity_field_ids.*' => ['required', 'integer', 'distinct', 'exists:activity_fields,id'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $role = DB::transaction(function () use ($validated): Role {
            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => $this->registry->guard(),
                'status' => $validated['status'] ?? 'active',
            ]);
            $role->syncPermissions($this->registry->expandDependencies($validated['permissions'] ?? []));
            $role->activityFields()->sync($validated['activity_field_ids']);

            return $role;
        });

        return $this->successResponse(
            ['role' => $role->load(['permissions:id,name', 'activityFields:id,name,slug,tag'])],
            'Role created successfully',
            201
        );
    }

    public function show(Request $request, Role $role): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-roles')) {
            return $error;
        }

        if ($role->guard_name !== $this->registry->guard()) {
            abort(404);
        }

        return $this->successResponse(
            ['role' => $role->load(['permissions:id,name', 'activityFields:id,name,slug,tag'])],
            'Role retrieved successfully'
        );
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'update-roles')) {
            return $error;
        }

        if ($role->guard_name !== $this->registry->guard()) {
            abort(404);
        }

        if (strcasecmp($role->name, 'SuperAdmin') === 0) {
            return $this->errorResponse('SuperAdmin role cannot be updated', 422);
        }

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where('guard_name', $this->registry->guard())
                    ->ignore($role->id),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'string',
                'distinct',
                Rule::exists('permissions', 'name')->where('guard_name', $this->registry->guard()),
            ],
            'activity_field_ids' => ['sometimes', 'array', 'min:1'],
            'activity_field_ids.*' => ['required_with:activity_field_ids', 'integer', 'distinct', 'exists:activity_fields,id'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        DB::transaction(function () use ($role, $validated): void {
            $role->update([
                'name' => $validated['name'] ?? $role->name,
                'status' => $validated['status'] ?? $role->status,
            ]);

            if (array_key_exists('permissions', $validated)) {
                $role->syncPermissions($this->registry->expandDependencies($validated['permissions'] ?? []));
            }

            if (array_key_exists('activity_field_ids', $validated)) {
                $role->activityFields()->sync($validated['activity_field_ids']);
            }
        });

        return $this->successResponse(
            ['role' => $role->load(['permissions:id,name', 'activityFields:id,name,slug,tag'])],
            'Role updated successfully'
        );
    }

    public function destroy(Request $request, Role $role): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'delete-roles')) {
            return $error;
        }

        if ($role->guard_name !== $this->registry->guard()) {
            abort(404);
        }

        if (strtolower($role->name) === 'superadmin') {
            return $this->errorResponse('SuperAdmin role cannot be deleted', 422);
        }

        $role->delete();

        return $this->successResponse(null, 'Role deleted successfully');
    }
}
