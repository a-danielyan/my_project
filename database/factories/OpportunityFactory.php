<?php

namespace Database\Factories;

use App\Models\CustomField;
use App\Models\CustomFieldValues;
use App\Models\Opportunity;
use App\Traits\FactoryCustomFieldPropertyTrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Opportunity>
 */
class OpportunityFactory extends Factory
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
            'created_by' => 1,
        ];
    }


    public function configure(): static
    {
        return $this->afterCreating(function (Opportunity $opportunity) {
            $allCustomFields = CustomField::query()->where('entity_type', Opportunity::class)
                ->where('type', '!=', 'container')->get();

            foreach ($allCustomFields as $field) {
                $createdData = array_merge([
                    'field_id' => $field->getKey(),
                    'entity_id' => $opportunity->getKey(),
                    'entity' => Opportunity::class,
                ], $this->getPropertyValue($field));

                CustomFieldValues::query()->create(
                    $createdData,
                );
            }
        });
    }
}
