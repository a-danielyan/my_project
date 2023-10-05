<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\PaymentProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PaymentProfileTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->getBaseSource() as $source) {
            PaymentProfile::query()->create($source);
        }
        Schema::enableForeignKeyConstraints();
    }

    private function getBaseSource(): array
    {
        return [
            [
                'account_id' => 1,
                'payment_name' => fake()->text,
                'payment_method' => fake()->randomElement([
                    Payment::PAYMENT_METHOD_CREDIT_CARD,
                    Payment::PAYMENT_METHOD_ACH,
                    Payment::PAYMENT_METHOD_CHECK,
                    Payment::PAYMENT_METHOD_CASH,
                ]),
                'billing_street_address' => fake()->streetAddress,
                'billing_city' => fake()->city,
                'billing_state' => 'test',
                'billing_postal_code' => fake()->postcode,
                'billing_country' => fake()->country,
                'created_by' => 1,
            ],
            [
                'account_id' => 1,
                'payment_name' => fake()->text,
                'payment_method' => fake()->randomElement([
                    Payment::PAYMENT_METHOD_CREDIT_CARD,
                    Payment::PAYMENT_METHOD_ACH,
                    Payment::PAYMENT_METHOD_CHECK,
                    Payment::PAYMENT_METHOD_CASH,
                ]),
                'billing_street_address' => fake()->streetAddress,
                'billing_city' => fake()->city,
                'billing_state' => 'test',
                'billing_postal_code' => fake()->postcode,
                'billing_country' => fake()->country,
                'created_by' => 1,
            ],
        ];
    }
}
