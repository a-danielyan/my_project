<?php

use Tests\TestCase;

class PreferenceTest extends TestCase
{
    /**
     * @return void
     */
    public function test_preference_returns_a_successful_response(): void
    {
        $response = $this->get(self::PREFERENCE_ROUTE . '?entity=Account');
        $response->assertStatus(200);
    }

    public function test_create_preference(): void
    {
        $response = $this->post(self::PREFERENCE_ROUTE, $this->getTestData());
        $response->assertStatus(200);
    }

    public function test_update_preference(): void
    {
        $response = $this->put(self::PREFERENCE_ROUTE . '/1', $this->getTestData());
        $response->assertStatus(200);
    }

    public function test_delete_preference(): void
    {
        $response = $this->delete(self::PREFERENCE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    private function getTestData(): array
    {
        return [
            "entity" => "Account",
            "name" => fake()->text(25),
            "settings" => [
                "filter" => [
                    ["key" => "val"],
                ],
            ],
        ];
    }
}
