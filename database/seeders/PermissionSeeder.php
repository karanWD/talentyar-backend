<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Services\AdminPermissionRegistry;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = app(AdminPermissionRegistry::class)->permissionNames();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'api-admin',
            ]);
        }
    }
}
