<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminRoleController extends BaseApiController
{
    private function authorizeOrFail(Request $request, string $permission): ?JsonResponse
    {
        $admin = $request->user('api-admin');
        if (! $admin || ! $admin->can($permission)) {
            return $this->errorResponse('Forbidden', 403);
        }

        return null;
    }

    public function index(Request $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-roles')) {
            return $error;
        }

        $roles = Role::query()->with('permissions:id,name')->paginate($request->integer('per_page', 15));

        return $this->successResponse(['roles' => $roles], 'Roles retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'create-roles')) {
            return $error;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
            'status' => $validated['status'] ?? 'active',
        ]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return $this->successResponse(['role' => $role->load('permissions:id,name')], 'Role created successfully', 201);
    }

    public function show(Request $request, Role $role): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-roles')) {
            return $error;
        }

        return $this->successResponse(['role' => $role->load('permissions:id,name')], 'Role retrieved successfully');
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'update-roles')) {
            return $error;
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $role->update([
            'name' => $validated['name'] ?? $role->name,
            'status' => $validated['status'] ?? $role->status,
        ]);

        if (array_key_exists('permissions', $validated)) {
            $role->syncPermissions($validated['permissions'] ?? []);
        }

        return $this->successResponse(['role' => $role->load('permissions:id,name')], 'Role updated successfully');
    }

    public function destroy(Request $request, Role $role): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'delete-roles')) {
            return $error;
        }

        if (strtolower($role->name) === 'superadmin') {
            return $this->errorResponse('SuperAdmin role cannot be deleted', 422);
        }

        $role->delete();

        return $this->successResponse(null, 'Role deleted successfully');
    }
}
