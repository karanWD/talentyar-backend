<?php

namespace Database\Seeders;

use App\Models\Ads;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

            // Seed all data for this tenant
            $this->call([PermissionSeeder::class]);
//            $this->call([CategorySeeder::class]);
            $this->call([RoleSeeder::class]);
            $this->call([AdminSeeder::class]);
//            $this->call([MediaSeeder::class]);
        $this->call([ProvinceSeeder::class]);
        $this->call([CitySeeder::class]);
        $this->call([IndustrySeeder::class]);
        $this->call([JobGroupSeeder::class]);
        $this->call([AdsSeeder::class]);
    }
}
