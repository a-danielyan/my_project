<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_the_users_list(): void
    {
        $response = $this->get(self::USER_ROUTE . '?limit=10&order=desc&page=1');
        $response->assertStatus(200);
    }

    public function test_get_single_user(): void
    {
        $response = $this->get(self::USER_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_update_user(): void
    {
        $response = $this->put(self::USER_ROUTE . '/1', ['description' => 'Test']);
        $response->assertStatus(200);
    }

    public function test_create_user(): void
    {
        $response = $this->post(self::USER_ROUTE, $this->getUserData());
        $response->assertStatus(200);
    }

    private function getUserData(): array
    {
        return [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => fake()->email,
            'roleId' => 1,
            'status' => User::STATUS_ACTIVE,
        ];
    }
}
