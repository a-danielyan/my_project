<?php

namespace Tests\Feature;

use Tests\TestCase;

class LeadStatusTest extends TestCase
{
    public function test_lead_status_list(): void
    {
        $response = $this->get(self::LEAD_STATUS_ROUTE);
        $response->assertStatus(200);
    }

    public function test_create_lead_status(): void
    {
        $response = $this->post(self::LEAD_STATUS_ROUTE, $this->getLeadStatusData());
        $response->assertStatus(200);
    }

    public function test_single_lead_status(): void
    {
        $response = $this->get(self::LEAD_STATUS_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_update_lead_status(): void
    {
        $response = $this->put(self::LEAD_STATUS_ROUTE . '/1', $this->getLeadStatusData());
        $response->assertStatus(200);
    }

    public function test_delete_lead_status(): void
    {
        $this->beginDatabaseTransaction();
        $response = $this->delete(self::LEAD_STATUS_ROUTE . '/1');
        $response->assertStatus(200);
    }

    private function getLeadStatusData(): array
    {
        return [
            'name' => fake()->text(20),
            'status' => 'Active',
        ];
    }
}
