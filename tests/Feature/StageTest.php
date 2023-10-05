<?php

namespace Tests\Feature;

use Tests\TestCase;

class StageTest extends TestCase
{
    public function test_stage_list(): void
    {
        $response = $this->get(self::STAGE_ROUTE);
        $response->assertStatus(200);
    }

    public function test_create_stage(): void
    {
        $response = $this->post(self::STAGE_ROUTE, $this->getStageData());
        $response->assertStatus(200);
    }

    public function test_single_stage(): void
    {
        $response = $this->get(self::STAGE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_update_stage(): void
    {
        $response = $this->put(self::STAGE_ROUTE . '/1', $this->getStageData());
        $response->assertStatus(200);
    }

    public function test_delete_stage(): void
    {
        $this->beginDatabaseTransaction();
        $response = $this->delete(self::STAGE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    private function getStageData(): array
    {
        return [
            'name' => fake()->text(20),
            'status' => 'Active',
        ];
    }
}
