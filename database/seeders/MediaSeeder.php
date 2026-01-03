<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\Category\Models\Category;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (\App\Models\Category::whereNull('parent_id')->get() as $key => $category) {
            $path = asset('assets/category/'.$category->slug.'.png');
            $media = $category->addMediaFromUrl($path, 'public', 'category');
        }

        foreach (\App\Models\Product::all() as $key => $product) {
                $path = asset('assets/product/img.png');
            try {
                $media = $product->addMediaFromUrl($path, 'public', 'product');
            }catch (\Exception $exception){

            }
        }
    }
}
