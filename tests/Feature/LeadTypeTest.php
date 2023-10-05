<?php

namespace Tests\Feature;

use Tests\TestCase;

class LeadTypeTest extends TestCase
{
    public function test_lead_type_list(): void
    {
        $response = $this->get(self::LEAD_TYPE_ROUTE);
        $response->assertStatus(200);
    }

    public function test_create_lead_type(): void
    {
        $response = $this->post(self::LEAD_TYPE_ROUTE, $this->getLeadTypeData());
        $response->assertStatus(200);
    }

    public function test_single_lead_type(): void
    {
        $response = $this->get(self::LEAD_TYPE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_update_lead_type(): void
    {
        $response = $this->put(self::LEAD_TYPE_ROUTE . '/1', $this->getLeadTypeData());
        $response->assertStatus(200);
    }

    public function test_delete_lead_type(): void
    {
        $this->beginDatabaseTransaction();
        $response = $this->delete(self::LEAD_TYPE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    private function getLeadTypeData(): array
    {
        return [
            'name' => fake()->text(20),
            'status' => 'Active',
        ];
    }
}
