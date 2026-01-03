<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

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
        ];

        // ایجاد دسترسی‌ها
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
