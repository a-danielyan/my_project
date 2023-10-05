<?php

namespace Database\Factories;

use App\Models\CustomField;
use App\Models\CustomFieldValues;
use App\Models\Lead;
use App\Traits\FactoryCustomFieldPropertyTrait;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    use FactoryCustomFieldPropertyTrait;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $availableSalutation = ['Mr.', 'Ms.', 'Dr.'];
        shuffle($availableSalutation);

        return [
            'salutation' => $availableSalutation[0],
            'created_by' => 1,
        ];
    }


    public function configure(): static
    {
        return $this->afterCreating(function (Lead $lead) {
            $allCustomFields = CustomField::query()->where('entity_type', Lead::class)
                ->where('type', '!=', 'container')->get();

            foreach ($allCustomFields as $field) {
                $createdData = array_merge([
                    'field_id' => $field->getKey(),
                    'entity_id' => $lead->getKey(),
                    'entity' => Lead::class,
                ], $this->getPropertyValue($field));

                CustomFieldValues::query()->create(
                    $createdData,
                );
            }
        });
    }
}
