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
            'create-categories',
            'read-categories',
            'update-categories',
            'delete-categories',

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
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}
