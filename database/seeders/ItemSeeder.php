<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            'کنترل توپ',
            'چابکی و تغییر مسیر',
            'سرعت با توپ',
            'پاس',
            'شوت',
        ];

        foreach ($items as $item) {
            Item::firstOrCreate(
                ['name' => $item, 'type' => 'football']
            );
        }
    }
}
