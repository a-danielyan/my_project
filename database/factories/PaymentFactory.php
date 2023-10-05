<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Payment;
use App\Traits\FactoryCustomFieldPropertyTrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
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
            'account_id' => Account::factory()->lazy(),
            'payment_name' => fake()->text(50),
            'invoice_id' => Invoice::factory()->lazy(),
            'payment_received' => fake()->randomFloat(2, 0, 500),
            'payment_method' => fake()->randomElement([
                Payment::PAYMENT_METHOD_CREDIT_CARD,
                Payment::PAYMENT_METHOD_ACH,
                Payment::PAYMENT_METHOD_CHECK,
                Payment::PAYMENT_METHOD_CASH,
            ]),
            'payment_date' => fake()->date(),
            'notes' => fake()->realText(),
            'received_by' => 1,
        ];
    }
}
