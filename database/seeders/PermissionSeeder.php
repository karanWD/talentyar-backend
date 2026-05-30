<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // دسته‌بندی‌ها
            'create-users',
            'read-users',
            'update-users',
            'delete-users',

            // ادمین‌ها
            'create-admins',
            'read-admins',
            'update-admins',
            'delete-admins',

            'create-roles',
            'read-roles',
            'update-roles',
            'delete-roles',

            // API admin ACL
            'read-permissions',

            // پست‌ها
            'read-posts',
            'update-posts',

            'read-evaluation',
            'update-evaluation',

            'read-items',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'api-admin',
            ]);
        }
    }
}
