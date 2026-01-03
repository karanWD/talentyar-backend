<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductPrice>
 */
class ProductPriceFactory extends Factory
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
            'min_qty' => $this->faker->numberBetween(1, 100),
            'max_qty' => $this->faker->numberBetween(101, 200),
            'price' => $this->faker->numberBetween(10000, 5000000),
            'discount' => $this->faker->randomElement([0, 5, 10, 15, 20]),
        ];
    }
}
