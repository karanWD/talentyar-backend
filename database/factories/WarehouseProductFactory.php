<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Warehouses;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WarehouseProduct>
 */
class WarehouseProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->id,
            'warehouse_id' => Warehouses::inRandomOrder()->first()->id,
            'quantity' => $this->faker->numberBetween(1, 100),
            'type' => $this->faker->randomElement(\App\Models\WarehouseProduct::TYPE),
        ];
    }
}
