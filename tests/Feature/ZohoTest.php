<?php

use Tests\TestCase;

class ZohoTest extends TestCase
{

    public function test_get_zoho_with_error(): void
    {
        $response = $this->get(self::ZOHO_ROUTE . '/authorization?error=Test');
        $response->assertStatus(422);
    }

    public function test_zoho_notification(): void
    {
        $response = $this->post(self::ZOHO_ROUTE . '/notification', ['job_id' => 1]);
        $response->assertStatus(200);
    }

    public function test_get_oauth_link(): void
    {
        $response = $this->get(self::ZOHO_ROUTE . '/oauthLink');
        $response->assertStatus(200)->assertJsonStructure(['link']);
    }
}
