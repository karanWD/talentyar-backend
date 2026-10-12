<?php

namespace App\Services;

use App\Models\Admin;

class AdminPermissionService
{
    /** @var array<int, list<string>> */
    private array $permissionCache = [];

    /** @var array<int, bool> */
    private array $superAdminCache = [];

    public function __construct(private readonly AdminPermissionRegistry $registry) {}

    public function can(?Admin $admin, string $permission): bool
    {
        if (! $admin || $admin->status !== 'active') {
            return false;
        }

        if ($this->isSuperAdmin($admin)) {
            return true;
        }

        return in_array($permission, $this->names($admin), true);
    }

    /**
     * Return direct permissions plus permissions inherited from active roles.
     *
     * @return list<string>
     */
    public function names(Admin $admin): array
    {
        if ($admin->status !== 'active') {
            return [];
        }

        if (isset($this->permissionCache[$admin->id])) {
            return $this->permissionCache[$admin->id];
        }

        $admin->loadMissing(['permissions', 'roles.permissions']);

        $permissions = $admin->permissions
            ->where('guard_name', $this->registry->guard())
            ->pluck('name')
            ->merge(
                $admin->roles
                    ->where('guard_name', $this->registry->guard())
                    ->where('status', 'active')
                    ->flatMap->permissions
                    ->where('guard_name', $this->registry->guard())
                    ->pluck('name')
            )
            ->unique()
            ->sort()
            ->values()
            ->all();

        return $this->permissionCache[$admin->id] = $permissions;
    }

    public function isSuperAdmin(Admin $admin): bool
    {
        if ($admin->status !== 'active') {
            return false;
        }

        if (isset($this->superAdminCache[$admin->id])) {
            return $this->superAdminCache[$admin->id];
        }

        $admin->loadMissing('roles');

        return $this->superAdminCache[$admin->id] = $admin->roles->contains(
            fn ($role): bool => $role->guard_name === $this->registry->guard()
                && $role->status === 'active'
                && strcasecmp($role->name, 'SuperAdmin') === 0
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function abilities(Admin $admin): array
    {
        $abilities = [];

        foreach ($this->registry->modules() as $moduleKey => $module) {
            foreach ($module['actions'] ?? [] as $action => $permission) {
                $abilities[$moduleKey][$action] = $this->can($admin, $permission['name']);
            }

            foreach ($module['fields'] ?? [] as $fieldKey => $field) {
                foreach ($field['permissions'] ?? [] as $action => $permission) {
                    $modulePermission = $this->registry->actionPermission($moduleKey, $action);
                    $abilities[$moduleKey]['fields'][$fieldKey][$action] = $modulePermission !== null
                        && $this->can($admin, $modulePermission)
                        && $this->can($admin, $permission['name'])
                        && collect($this->registry->requirements($permission['name']))
                            ->every(fn (string $required): bool => $this->can($admin, $required));
                }
            }
        }

        return $abilities;
    }
}
