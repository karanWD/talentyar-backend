<?php

namespace App\Services;

use App\Models\Admin;

class AdminFieldPermissionService
{
    public function __construct(
        private readonly AdminPermissionRegistry $registry,
        private readonly AdminPermissionService $permissions,
    ) {}

    public function canView(Admin $admin, string $module, string $field): bool
    {
        return $this->can($admin, $module, $field, 'view');
    }

    public function canUpdate(Admin $admin, string $module, string $field): bool
    {
        return $this->can($admin, $module, $field, 'update');
    }

    /**
     * @param  list<string>  $fields
     * @return list<string>
     */
    public function forbiddenFields(Admin $admin, string $module, array $fields, string $action): array
    {
        return array_values(array_filter(
            $fields,
            fn (string $field): bool => $this->registry->fieldPermission($module, $field, $action) !== null
                && ! $this->can($admin, $module, $field, $action)
        ));
    }

    private function can(Admin $admin, string $module, string $field, string $action): bool
    {
        $modulePermission = $this->registry->actionPermission($module, $action);
        $fieldPermission = $this->registry->fieldPermission($module, $field, $action);

        if (! $modulePermission || ! $fieldPermission) {
            return false;
        }

        return $this->permissions->can($admin, $modulePermission)
            && $this->permissions->can($admin, $fieldPermission)
            && collect($this->registry->requirements($fieldPermission))
                ->every(fn (string $required): bool => $this->permissions->can($admin, $required));
    }
}
