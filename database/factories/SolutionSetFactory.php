<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SolutionSet>
 */
class SolutionSetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_by' => 1,
            'name' => fake()->text,
        ];
    }
}
