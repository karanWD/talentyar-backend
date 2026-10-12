<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Admin;
use App\Models\Permission;
use App\Services\AdminPermissionRegistry;
use App\Services\AdminPermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPermissionController extends BaseApiController
{
    public function __construct(
        private readonly AdminPermissionRegistry $registry,
        private readonly AdminPermissionService $permissions,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $admin = $request->user('api-admin');

        if (! $admin instanceof Admin || ! $this->permissions->can($admin, 'read-permissions')) {
            return $this->errorResponse('Forbidden', 403);
        }

        $modules = $this->registry->catalogue();
        $unregisteredPermissions = Permission::query()
            ->where('guard_name', $this->registry->guard())
            ->whereNotIn('name', $this->registry->permissionNames())
            ->orderBy('name')
            ->pluck('name')
            ->map(fn (string $name): array => [
                'action' => 'custom',
                'name' => $name,
                'label' => $name,
            ])
            ->values()
            ->all();

        if ($unregisteredPermissions !== []) {
            $modules[] = [
                'key' => 'other',
                'label' => 'سایر دسترسی‌ها',
                'actions' => $unregisteredPermissions,
                'fields' => [],
            ];
        }

        return $this->successResponse(
            ['modules' => $modules],
            'Permissions retrieved successfully'
        );
    }
}
