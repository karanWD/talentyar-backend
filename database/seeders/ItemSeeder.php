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
        Item::firstOrCreate(
            ['name' => 'قدرت حمل توپ', 'type' => 'football']
        );
        Item::firstOrCreate(
            ['name' => 'قدرت پاس', 'type' => 'football']
        );
        Item::firstOrCreate(
            ['name' => 'قدرت شوت', 'type' => 'football']
        );
        Item::firstOrCreate(
            ['name' => 'دقت شوت', 'type' => 'football']
        );
    }

}
