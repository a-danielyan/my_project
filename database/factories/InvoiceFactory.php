<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Contact;
use App\Models\Estimate;
use App\Models\Opportunity;
use App\Traits\FactoryCustomFieldPropertyTrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
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
            'opportunity_id' => Opportunity::factory()->lazy(),
            'estimate_id' => Estimate::factory()->lazy(),
            'account_id' => Account::factory()->lazy(),
            'contact_id' => Contact::factory()->lazy(),
            'sub_total' => fake()->randomFloat(2, 0, 100000),
            'total_tax' => fake()->randomFloat(2, 0, 100000),
            'total_discount' => fake()->randomFloat(2, 0, 100000),
            'grand_total' => fake()->randomFloat(2, 0, 100000),
            'balance_due' => fake()->randomFloat(2, 0, 500),
        ];
    }


    /*   public function configure(): static
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
       }*/
}
