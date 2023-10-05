<?php

namespace Tests\Feature;

use Tests\TestCase;

class IndustryTest extends TestCase
{
    public function test_industry_list(): void
    {
        $response = $this->get(self::INDUSTRY_ROUTE);
        $response->assertStatus(200);
    }

    public function test_create_industry(): void
    {
        $response = $this->post(self::INDUSTRY_ROUTE, $this->getIndustryData());
        $response->assertStatus(200);
    }

    public function test_single_industry(): void
    {
        $response = $this->get(self::INDUSTRY_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_update_industry(): void
    {
        $response = $this->put(self::INDUSTRY_ROUTE . '/1', $this->getIndustryData());
        $response->assertStatus(200);
    }

    public function test_delete_industry(): void
    {
        $this->beginDatabaseTransaction();
        $response = $this->delete(self::INDUSTRY_ROUTE . '/1');
        $response->assertStatus(200);
    }

    private function getIndustryData(): array
    {
        return [
            'name' => fake()->text(20),
            'status' => 'Active',
        ];
    }
}
