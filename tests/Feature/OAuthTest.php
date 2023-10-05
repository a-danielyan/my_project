<?php

use Tests\TestCase;

class OAuthTest extends TestCase
{

    public function test_get_tokens(): void
    {
        $response = $this->get(self::OAUTH_ROUTE . '?service=zohocrm');
        $response->assertStatus(200);
    }

    public function test_delete_token(): void
    {
        $response = $this->delete(self::OAUTH_ROUTE . '/1');
        $response->assertStatus(200);
    }
}
