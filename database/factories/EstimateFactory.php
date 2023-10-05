<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Contact;
use App\Models\CustomField;
use App\Models\CustomFieldValues;
use App\Models\Estimate;
use App\Models\Opportunity;
use App\Traits\FactoryCustomFieldPropertyTrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Estimate>
 */
class EstimateFactory extends Factory
{
    use FactoryCustomFieldPropertyTrait;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $opportunityId = Opportunity::query()->inRandomOrder()->first()->getKey();
        $accountId = Account::query()->inRandomOrder()->first()->getKey();
        $contactId = Contact::query()->inRandomOrder()->first()->getKey();

        return [
            'created_by' => 1,
            'opportunity_id' => $opportunityId,
            'sub_total' => fake()->randomFloat(2, 0, 100000),
            'total_tax' => fake()->randomFloat(2, 0, 100000),
            'total_discount' => fake()->randomFloat(2, 0, 100000),
            'grand_total' => fake()->randomFloat(2, 0, 100000),
            'account_id' => $accountId,
            'contact_id' => $contactId,
        ];
    }


    public function configure(): static
    {
        return $this->afterCreating(function (Estimate $estimate) {
            $allCustomFields = CustomField::query()->where('entity_type', Estimate::class)
                ->where('type', '!=', 'container')->get();

            foreach ($allCustomFields as $field) {
                $createdData = array_merge([
                    'field_id' => $field->getKey(),
                    'entity_id' => $estimate->getKey(),
                    'entity' => Estimate::class,
                ], $this->getPropertyValue($field));

                CustomFieldValues::query()->create(
                    $createdData,
                );
            }
        });
    }
}
