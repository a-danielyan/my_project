<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->name() . time(),
            'last_name' => fake()->lastName() . time(),
            'email' => fake()->unique()->safeEmail() . time(),
            'email_verified_at' => now(),
            'role_id' => 1,
            'remember_token' => Str::random(10),
        ];
    }
}
