<?php

use Tests\TestCase;

class EntityLogTest extends TestCase
{
    public function test_get_lead_entity_log(): void
    {
        $response = $this->get(self::LOG_ROUTE . '/Lead/1');
        $response->assertStatus(200);
    }
}
