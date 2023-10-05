<?php

namespace Tests\Feature;

use Tests\TestCase;

class SolutionSetTest extends TestCase
{
    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_solution_set_returns_a_successful_response(string $input): void
    {
        $response = $this->get(self::SOLUTION_SET_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    public function test_get_solution_set(): void
    {
        $response = $this->get(self::SOLUTION_SET_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_create_solution_set(): void
    {
        $response = $this->post(self::SOLUTION_SET_ROUTE, $this->getTestData());
        $response->assertStatus(200);
    }

    public function test_update_solution_set(): void
    {
        $response = $this->put(self::SOLUTION_SET_ROUTE . '/1', $this->getTestData());
        $response->assertStatus(200);
    }

    public function test_delete_solution_set(): void
    {
        $response = $this->delete(self::SOLUTION_SET_ROUTE . '/1');
        $response->assertStatus(200);
        $response = $this->get(self::SOLUTION_SET_ROUTE . '/1');
        $response->assertStatus(404);
    }


    private function getTestData(): array
    {
        return [
            'name' => fake()->text,
            'items' => [
                [
                    'productId' => 1,
                    'quantity' => fake()->randomFloat(2, 1, 5),
                    'price' => fake()->randomFloat(2, 10, 50),
                    'discount' => fake()->randomFloat(2, 0, 1),
                    'description' => 'test description',
                ],
                [
                    'productId' => 2,
                    'quantity' => fake()->randomFloat(2, 1, 5),
                    'price' => fake()->randomFloat(2, 10, 50),
                    'discount' => fake()->randomFloat(2, 0, 1),
                    'description' => 'test description2',
                ],
            ],
        ];
    }

    public static function getSortingList(): array
    {
        return [
            ['name'],
            ['next-step',],
        ];
    }
}
