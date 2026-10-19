<?php

namespace Database\Seeders;

use App\Models\ActivityField;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = Permission::query()->where('guard_name', 'api-admin')->pluck('name')->toArray();
        $roleSuperAdmin = Role::firstOrCreate([
            'name' => 'SuperAdmin',
            'guard_name' => 'api-admin',
        ], [
            'status' => 'active',
        ]);
        $roleSuperAdmin->syncPermissions($permissions);
        $roleSuperAdmin->activityFields()->sync(ActivityField::query()->pluck('id'));
    }
}
