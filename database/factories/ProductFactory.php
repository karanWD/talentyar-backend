<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Unit;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code'        => function () {
                $code = str_pad($this->faker->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT);
                return 'PRD' . $code;
            },
            'name'        => function () {
                $name = $this->faker->words(rand(1, 2), true);
                return $name;
            },
            'description' => $this->faker->sentence(12),
            'view_count'  => $this->faker->numberBetween(0, 5000),
            'status'      => $this->faker->randomElement(['active', 'inactive']),
            'sale'        => $this->faker->randomElement(['active', 'inactive']),
            'label'       => $this->faker->randomElement(Product::LABEL),
            'unit_id'     => Unit::inRandomOrder()->first()->id,
            'step_count'  => $this->faker->numberBetween(1, 10),
            'priority'    => $this->faker->numberBetween(0, 100),
        ];
    }
}
