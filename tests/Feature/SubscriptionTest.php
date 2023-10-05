<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_subscription_returns_a_successful_response(string $input): void
    {
        $response = $this->get(self::SUBSCRIPTION_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_subscription_returns_a_successful_response_with_search(string $input): void
    {
        $this->markTestSkipped('Currently we dont use search. Maybe need later');
        $searchString = fake()->text(10);
        $response = $this->get(
            self::SUBSCRIPTION_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1&search=' . $searchString,
        );
        $response->assertStatus(200);
    }

    public function test_get_subscription(): void
    {
        $response = $this->get(self::SUBSCRIPTION_ROUTE . '/1');
        $response->assertStatus(200);
    }


    public static function getSortingList(): array
    {
        return [
            ['status'],
            ['subscription_name'],
        ];
    }
}
