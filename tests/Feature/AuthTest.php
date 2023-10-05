<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * @return void
     */
    public function test_get_me(): void
    {
        $response = $this->get(self::ME_ROUTE);
        $response->assertStatus(200);
        $response->assertJsonStructure(['firstName', 'role']);
    }

    public function test_get_refresh(): void
    {
        $response = $this->post(self::REFRESH_ROUTE);
        $response->assertStatus(200);
    }

    public function test_update(): void
    {
        $response = $this->put(self::ME_ROUTE, ['firstName' => 'John']);
        $response->assertStatus(200);
    }

    public function test_get_logout(): void
    {
        $response = $this->post(self::LOGOUT_ROUTE);
        $response->assertStatus(200);
        $response = $this->get(self::ME_ROUTE);
        $response->assertUnauthorized();
    }
}
