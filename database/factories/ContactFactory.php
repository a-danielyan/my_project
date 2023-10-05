<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Contact;
use App\Models\CustomField;
use App\Models\CustomFieldValues;
use App\Traits\FactoryCustomFieldPropertyTrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
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
            'account_id' => 1,
            'status' => 'Active',
        ];
    }


    public function configure(): static
    {
        return $this->afterCreating(function (Contact $contact) {
            $allCustomFields = CustomField::query()->where('entity_type', Contact::class)
                ->where('type', '!=', 'container')->get();

            foreach ($allCustomFields as $field) {
                $createdData = array_merge([
                    'field_id' => $field->getKey(),
                    'entity_id' => $contact->getKey(),
                    'entity' => Contact::class,
                ], $this->getPropertyValue($field));

                CustomFieldValues::query()->create(
                    $createdData,
                );
            }
        });
    }
}
