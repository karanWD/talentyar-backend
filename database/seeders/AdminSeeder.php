<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $username = env('SUPER_ADMIN_USERNAME', 'superadmin');
        $password = env('SUPER_ADMIN_PASSWORD', '12345678');
        $phone = env('SUPER_ADMIN_PHONE', '09120000000');

        $admin1 = Admin::firstOrCreate(
            ['username' => $username],
            [
                'name' => 'Super Admin',
                'phone' => $phone,
                'status' => 'active',
                'password' => $password,
            ]
        );

        if (!$admin1->hasRole('SuperAdmin')) {
            $admin1->assignRole('SuperAdmin');
        }
    }
}
