<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = Permission::query()->where('guard_name', 'web')->pluck('name')->toArray();
        $roleSuperAdmin = Role::firstOrCreate([
            'name' => 'SuperAdmin',
            'guard_name' => 'web',
        ], [
            'status' => 'active',
        ]);
        $roleSuperAdmin->syncPermissions($permissions);
    }
}
