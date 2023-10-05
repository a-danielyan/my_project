<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Payment;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_payment_returns_a_successful_response(string $input): void
    {
        $response = $this->get(self::PAYMENT_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_payment_returns_a_successful_response_with_search(string $input): void
    {
        $this->markTestSkipped('Currently we dont use search. Maybe need later');
        $searchString = fake()->text(10);
        $response = $this->get(
            self::PAYMENT_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1&search=' . $searchString,
        );
        $response->assertStatus(200);
    }

    public function test_get_payment(): void
    {
        $response = $this->get(self::PAYMENT_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_save_payment(): void
    {
        $response = $this->post(self::PAYMENT_ROUTE, $this->getTestData());
        $response->assertStatus(200);
    }

    public function test_update_payment(): void
    {
        $response = $this->put(self::PAYMENT_ROUTE . '/1', ['paymentMethod' => Payment::PAYMENT_METHOD_CASH]);
        $response->assertStatus(200);
    }

    public static function getSortingList(): array
    {
        return [
            ['accountId'],
            ['paymentName'],
            ['invoiceId'],
        ];
    }

    public function getTestData(): array
    {
        return [
            'accountId' => 1,
            'invoiceId' => 1,
            'paymentReceived' => fake()->randomFloat(2, 1, 5),
            'paymentMethod' => fake()->randomElement([
                Payment::PAYMENT_METHOD_ACH,
                Payment::PAYMENT_METHOD_CHECK,
                Payment::PAYMENT_METHOD_CASH,
                Payment::PAYMENT_METHOD_WIRE,
            ]),
            'note' => fake()->text,
            'paymentDate' => now(),
            'receivedBy' => 1,
        ];
    }
}
