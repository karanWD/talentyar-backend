<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Services\TenantService;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $admin1 = Admin::firstOrCreate(
            ['phone' => '09198829176'],
            [
                'name' => 'سیدسیناحسینی',
                'password' => bcrypt('123456'),
            ]
        );

        if (!$admin1->hasRole('SuperAdmin')) {
            $admin1->assignRole('SuperAdmin');
        }
    }
}
