<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
            ProvinceSeeder::class,
            CitySeeder::class,
            PlanSeeder::class,
            UserSeeder::class,
            PostCommentLikeSeeder::class,
        ]);
    }
}
