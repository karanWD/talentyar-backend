<?php

namespace App\Services;

use Illuminate\Support\Arr;

class AdminPermissionRegistry
{
    public function guard(): string
    {
        return (string) config('admin_permissions.guard', 'api-admin');
    }

    /**
     * @return array<string, mixed>
     */
    public function modules(): array
    {
        return config('admin_permissions.modules', []);
    }

    public function actionPermission(string $module, string $action): ?string
    {
        return Arr::get($this->modules(), "{$module}.actions.{$action}.name");
    }

    public function fieldPermission(string $module, string $field, string $action): ?string
    {
        return Arr::get($this->modules(), "{$module}.fields.{$field}.permissions.{$action}.name");
    }

    /**
     * @return list<string>
     */
    public function requirements(string $permissionName): array
    {
        foreach ($this->modules() as $module) {
            foreach ($module['fields'] ?? [] as $field) {
                foreach ($field['permissions'] ?? [] as $permission) {
                    if ($permission['name'] === $permissionName) {
                        return $permission['requires'] ?? [];
                    }
                }
            }
        }

        return [];
    }

    /**
     * @return list<string>
     */
    public function permissionNames(): array
    {
        $names = [];

        foreach ($this->modules() as $module) {
            foreach ($module['actions'] ?? [] as $permission) {
                $names[] = $permission['name'];
            }

            foreach ($module['fields'] ?? [] as $field) {
                foreach ($field['permissions'] ?? [] as $permission) {
                    $names[] = $permission['name'];
                }
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * Add declared permission dependencies while preserving the submitted order.
     *
     * @param  list<string>  $permissions
     * @return list<string>
     */
    public function expandDependencies(array $permissions): array
    {
        $dependencies = [];

        foreach ($this->modules() as $module) {
            foreach ($module['fields'] ?? [] as $field) {
                foreach ($field['permissions'] ?? [] as $permission) {
                    $dependencies[$permission['name']] = $permission['requires'] ?? [];
                }
            }
        }

        $expanded = array_values(array_unique($permissions));

        do {
            $changed = false;

            foreach ($expanded as $permission) {
                foreach ($dependencies[$permission] ?? [] as $required) {
                    if (! in_array($required, $expanded, true)) {
                        $expanded[] = $required;
                        $changed = true;
                    }
                }
            }
        } while ($changed);

        return $expanded;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function catalogue(): array
    {
        $catalogue = [];

        foreach ($this->modules() as $moduleKey => $module) {
            $actions = [];
            foreach ($module['actions'] ?? [] as $action => $permission) {
                $actions[] = [
                    'action' => $action,
                    ...$permission,
                ];
            }

            $fields = [];
            foreach ($module['fields'] ?? [] as $fieldKey => $field) {
                $fieldPermissions = [];
                foreach ($field['permissions'] ?? [] as $action => $permission) {
                    $fieldPermissions[] = [
                        'action' => $action,
                        'name' => $permission['name'],
                        'label' => $permission['label'],
                        'requires' => $permission['requires'] ?? [],
                    ];
                }

                $fields[] = [
                    'key' => $fieldKey,
                    'label' => $field['label'],
                    'permissions' => $fieldPermissions,
                ];
            }

            $catalogue[] = [
                'key' => $moduleKey,
                'label' => $module['label'],
                'actions' => $actions,
                'fields' => $fields,
            ];
        }

        return $catalogue;
    }
}
