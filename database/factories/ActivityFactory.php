<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Traits\FactoryCustomFieldPropertyTrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
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
            'related_to' => 1,
            'started_at' => now(),
            'ended_at' => now(),
            'activity_type' => fake()->randomElement(Activity::ACTIVITY_TYPES),
            'activity_status' => fake()->randomElement(Activity::ACTIVITY_STATUSES),
            'subject' => fake()->text(50),
            'related_to_entity' => 'App\Models\Lead',
            'related_to_id' => 1,
            'description' => fake()->text(50),
            'created_by' => 1,
        ];
    }
}
