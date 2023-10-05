<?php

namespace Tests\Feature;

use Tests\TestCase;

class LeadSourceTest extends TestCase
{
    public function test_lead_source_list(): void
    {
        $response = $this->get(self::LEAD_SOURCE_ROUTE);
        $response->assertStatus(200);
    }

    public function test_create_lead_source(): void
    {
        $response = $this->post(self::LEAD_SOURCE_ROUTE, $this->getLeadSourceData());
        $response->assertStatus(200);
    }

    public function test_single_lead_source(): void
    {
        $response = $this->get(self::LEAD_SOURCE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_update_lead_source(): void
    {
        $response = $this->put(self::LEAD_SOURCE_ROUTE . '/1', $this->getLeadSourceData());
        $response->assertStatus(200);
    }

    public function test_delete_lead_source(): void
    {
        $this->beginDatabaseTransaction();
        $response = $this->delete(self::LEAD_SOURCE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    private function getLeadSourceData(): array
    {
        return [
            'name' => fake()->text(20),
            'status' => 'Active',
        ];
    }
}
