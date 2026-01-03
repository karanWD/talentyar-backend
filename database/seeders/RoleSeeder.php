<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = Permission::all()->pluck('name')->toArray();
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $roleSuperAdmin->syncPermissions($permissions);

        $categories = Category::with('children')
        ->whereNull('parent_id')
        ->pluck('id')
        ->toArray();

        $syncData = [];
        foreach ($categories as $categoryId) {
            $syncData[$categoryId] = [
                'permission_level' => Role::PERMISSION_LEVEL_ALL,
            ];
        }
        $roleSuperAdmin->categories()->sync($syncData);

    }
}
