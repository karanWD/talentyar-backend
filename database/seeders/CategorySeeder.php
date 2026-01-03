<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Services\TenantService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = TenantService::getTenantId();
        
        // Create parent categories
        $electrode = Category::firstOrCreate(
            ['slug' => 'electrode', 'tenant_id' => $tenantId],
            ['name' => 'الکترود']
        );

        $glue123 = Category::firstOrCreate(
            ['slug' => 'glue-123', 'tenant_id' => $tenantId],
            ['name' => 'چسب ۱۲۳']
        );

        $rabits = Category::firstOrCreate(
            ['slug' => 'rabits', 'tenant_id' => $tenantId],
            ['name' => 'رابیتس']
        );

        $maftool = Category::firstOrCreate(
            ['slug' => 'maftool', 'tenant_id' => $tenantId],
            ['name' => 'مفتول']
        );

        $siliconeGlue = Category::firstOrCreate(
            ['slug' => 'silicone-glue', 'tenant_id' => $tenantId],
            ['name' => 'چسب سلیکون']
        );

        $stoneDisk = Category::firstOrCreate(
            ['slug' => 'stone-disk', 'tenant_id' => $tenantId],
            ['name' => 'صفحه سنگ']
        );

        $copperWire = Category::firstOrCreate(
            ['slug' => 'copper-wire', 'tenant_id' => $tenantId],
            ['name' => 'سیم برنج']
        );

        $sack = Category::firstOrCreate(
            ['slug' => 'sack', 'tenant_id' => $tenantId],
            ['name' => 'گونی']
        );

        // Create child categories for electrode
        $electrode1 = Category::firstOrCreate(
            ['slug' => 'electrode1', 'tenant_id' => $tenantId],
            [
                'name' => 'الکترود1',
                'parent_id' => $electrode->id
            ]
        );

        $electrode2 = Category::firstOrCreate(
            ['slug' => 'electrode2', 'tenant_id' => $tenantId],
            [
                'name' => 'الکترود2',
                'parent_id' => $electrode->id
            ]
        );

        $electrode3 = Category::firstOrCreate(
            ['slug' => 'electrode3', 'tenant_id' => $tenantId],
            [
                'name' => 'الکترود3',
                'parent_id' => $electrode->id
            ]
        );

        $electrode4 = Category::firstOrCreate(
            ['slug' => 'electrode4', 'tenant_id' => $tenantId],
            [
                'name' => 'الکترود4',
                'parent_id' => $electrode->id
            ]
        );

        // Create grandchild categories
        Category::firstOrCreate(
            ['slug' => 'electrode44', 'tenant_id' => $tenantId],
            [
                'name' => 'الکترود44',
                'parent_id' => $electrode3->id
            ]
        );

        Category::firstOrCreate(
            ['slug' => 'electrode55', 'tenant_id' => $tenantId],
            [
                'name' => 'الکترود55',
                'parent_id' => $electrode3->id
            ]
        );
    }

}
