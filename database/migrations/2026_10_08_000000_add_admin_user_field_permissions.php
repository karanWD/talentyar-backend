<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * @var array<string, list<string>>
     */
    private array $inheritance = [
        'read-users' => [
            'users.fields.phone.view',
            'users.fields.email.view',
            'users.fields.birth_date.view',
            'users.fields.subscription.view',
            'users.fields.referral_code.view',
        ],
        'update-users' => [
            'users.fields.phone.view',
            'users.fields.phone.update',
            'users.fields.email.view',
            'users.fields.email.update',
            'users.fields.birth_date.view',
            'users.fields.birth_date.update',
        ],
    ];

    public function up(): void
    {
        $tables = config('permission.table_names');
        $columns = config('permission.column_names');
        $guard = 'api-admin';
        $permissionPivot = $columns['permission_pivot_key'] ?? 'permission_id';
        $rolePivot = $columns['role_pivot_key'] ?? 'role_id';

        DB::transaction(function () use ($tables, $guard, $permissionPivot, $rolePivot): void {
            $now = now();
            $newPermissions = array_values(array_unique(array_merge(...array_values($this->inheritance))));

            DB::table($tables['permissions'])->insertOrIgnore(
                array_map(fn (string $name): array => [
                    'name' => $name,
                    'guard_name' => $guard,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $newPermissions)
            );

            $permissionIds = DB::table($tables['permissions'])
                ->where('guard_name', $guard)
                ->whereIn('name', array_merge(array_keys($this->inheritance), $newPermissions))
                ->pluck('id', 'name');

            foreach ($this->inheritance as $sourceName => $targetNames) {
                $sourceId = $permissionIds->get($sourceName);

                if (! $sourceId) {
                    continue;
                }

                $roleIds = DB::table($tables['role_has_permissions'])
                    ->where($permissionPivot, $sourceId)
                    ->pluck($rolePivot);
                $directAssignments = DB::table($tables['model_has_permissions'])
                    ->where($permissionPivot, $sourceId)
                    ->get();

                foreach ($targetNames as $targetName) {
                    $targetId = $permissionIds->get($targetName);

                    if (! $targetId) {
                        continue;
                    }

                    DB::table($tables['role_has_permissions'])->insertOrIgnore(
                        $roleIds->map(fn ($roleId): array => [
                            $permissionPivot => $targetId,
                            $rolePivot => $roleId,
                        ])->all()
                    );

                    DB::table($tables['model_has_permissions'])->insertOrIgnore(
                        $directAssignments->map(function ($assignment) use ($permissionPivot, $targetId): array {
                            $row = (array) $assignment;
                            $row[$permissionPivot] = $targetId;

                            return $row;
                        })->all()
                    );
                }
            }
        });

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $tables = config('permission.table_names');
        $names = array_values(array_unique(array_merge(...array_values($this->inheritance))));

        DB::table($tables['permissions'])
            ->where('guard_name', 'api-admin')
            ->whereIn('name', $names)
            ->delete();

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
