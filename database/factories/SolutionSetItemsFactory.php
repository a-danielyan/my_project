<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\SolutionSet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SolutionSetItems>
 */
class SolutionSetItemsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'solution_set_id' => SolutionSet::factory()->lazy(),
            'product_id' => Product::factory()->lazy(),
            'quantity' => fake()->randomFloat(2, 1, 10),
            'price' => fake()->randomFloat(2, 10, 100),
            'description' => fake()->text,
            'discount' => fake()->randomFloat(2, 0, 10),
        ];
    }
}
