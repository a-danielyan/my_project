<?php

namespace Tests\Feature;

use Tests\TestCase;

class SolutionTest extends TestCase
{
    public function test_solution_list(): void
    {
        $response = $this->get(self::SOLUTION_ROUTE);
        $response->assertStatus(200);
    }

    public function test_create_solution(): void
    {
        $response = $this->post(self::SOLUTION_ROUTE, $this->getSolutionData());
        $response->assertStatus(200);
    }

    public function test_single_solution(): void
    {
        $response = $this->get(self::SOLUTION_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_update_solution(): void
    {
        $response = $this->put(self::SOLUTION_ROUTE . '/1', $this->getSolutionData());
        $response->assertStatus(200);
    }

    public function test_delete_solution(): void
    {
        $this->beginDatabaseTransaction();
        $response = $this->delete(self::SOLUTION_ROUTE . '/1');
        $response->assertStatus(200);
    }

    private function getSolutionData(): array
    {
        return [
            'name' => fake()->text(20),
            'status' => 'Active',
        ];
    }
}
