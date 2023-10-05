<?php

use App\Models\Payment;
use Tests\TestCase;

class PaymentProfileTest extends TestCase
{
    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_the_payment_profiles_returns_a_successful_response(string $input): void
    {
        $response = $this->get(self::PAYMENT_PROFILE_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    public function test_get_payment_profile(): void
    {
        $response = $this->get(self::PAYMENT_PROFILE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_create_payment_profile(): void
    {
        $response = $this->post(self::PAYMENT_PROFILE_ROUTE, $this->getTestData());
        $response->assertStatus(200);
    }

    public function test_update_payment_profile(): void
    {
        $response = $this->put(self::PAYMENT_PROFILE_ROUTE . '/1', $this->getTestData());
        $response->assertStatus(200);
    }

    public function test_delete_payment_profile(): void
    {
        $response = $this->delete(self::PAYMENT_PROFILE_ROUTE . '/1');
        $response->assertStatus(200);
        $response = $this->get(self::PAYMENT_PROFILE_ROUTE . '/1');
        $response->assertStatus(404);
    }

    private function getTestData(): array
    {
        return [
            'accountId' => 1,
            'paymentName'=>fake()->text,
            'paymentMethod'=>fake()->randomElement([
                Payment::PAYMENT_METHOD_CREDIT_CARD,
                Payment::PAYMENT_METHOD_ACH,
                Payment::PAYMENT_METHOD_CHECK,
                Payment::PAYMENT_METHOD_CASH,
            ]),
            'billingStreetAddress'=>fake()->streetAddress,
            'billingCity'=>fake()->city,
            'billingState'=>'test',
            'billingPostalCode'=>fake()->postcode,
            'billingCountry'=>fake()->country,
        ];
    }

    public static function getSortingList(): array
    {
        return [
            ['account_id'],
            ['payment_method'],
            ['created_at'],
        ];
    }
}
