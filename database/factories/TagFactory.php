<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Traits\FactoryCustomFieldPropertyTrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
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
            'tag' => fake()->text(20),
            'text_color' => fake()->rgbaCssColor,
            'entity_type' => 'App\Models\\' . fake()->randomElement(Tag::AVAILABLE_ENTITY),
            'created_by' => 1,
        ];
    }
}
