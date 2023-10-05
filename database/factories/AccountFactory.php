<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\CustomField;
use App\Models\CustomFieldValues;
use App\Traits\FactoryCustomFieldPropertyTrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
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
            'status' => 'Active',
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Account $account) {
            $allCustomFields = CustomField::query()->where('entity_type', Account::class)
                ->where('type', '!=', 'container')->get();

            foreach ($allCustomFields as $field) {
                $createdData = array_merge([
                    'field_id' => $field->getKey(),
                    'entity_id' => $account->getKey(),
                    'entity' => Account::class,
                ], $this->getPropertyValue($field));

                CustomFieldValues::query()->create(
                    $createdData,
                );
            }
        });
    }
}
