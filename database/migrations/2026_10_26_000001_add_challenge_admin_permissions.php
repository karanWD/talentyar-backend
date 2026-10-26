<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** @var list<string> */
    private array $permissions = [
        'create-challenges',
        'read-challenges',
        'update-challenges',
        'delete-challenges',
    ];

    public function up(): void
    {
        $tables = config('permission.table_names');
        $columns = config('permission.column_names');
        $permissionPivot = $columns['permission_pivot_key'] ?? 'permission_id';
        $rolePivot = $columns['role_pivot_key'] ?? 'role_id';
        $now = now();

        DB::transaction(function () use ($tables, $permissionPivot, $rolePivot, $now): void {
            DB::table($tables['permissions'])->insertOrIgnore(
                array_map(fn (string $name): array => [
                    'name' => $name,
                    'guard_name' => 'api-admin',
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $this->permissions)
            );

            $permissionIds = DB::table($tables['permissions'])
                ->where('guard_name', 'api-admin')
                ->whereIn('name', $this->permissions)
                ->pluck('id');
            $superAdminRoleIds = DB::table($tables['roles'])
                ->where('guard_name', 'api-admin')
                ->whereRaw('LOWER(name) = ?', ['superadmin'])
                ->pluck('id');

            foreach ($superAdminRoleIds as $roleId) {
                DB::table($tables['role_has_permissions'])->insertOrIgnore(
                    $permissionIds->map(fn ($permissionId): array => [
                        $permissionPivot => $permissionId,
                        $rolePivot => $roleId,
                    ])->all()
                );
            }
        });

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $tables = config('permission.table_names');

        DB::table($tables['permissions'])
            ->where('guard_name', 'api-admin')
            ->whereIn('name', $this->permissions)
            ->delete();

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
