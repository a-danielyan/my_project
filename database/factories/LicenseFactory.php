<?php

namespace Database\Factories;

use App\Models\License;
use App\Traits\FactoryCustomFieldPropertyTrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\License>
 */
class LicenseFactory extends Factory
{
    use FactoryCustomFieldPropertyTrait;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->text(50),
            'license_type' => fake()->randomElement(License::AVAILABLE_LICENSE_TYPES),
            'license_duration_in_month' => fake()->numberBetween(1, 10),
            'created_by' => 1,
        ];
    }
}
