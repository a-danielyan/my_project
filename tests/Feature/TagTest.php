<?php

namespace Tests\Feature;

use Tests\TestCase;

class TagTest extends TestCase
{
    /**
     * @return void
     */
    public function test_tag_returns_a_successful_response(): void
    {
        $response = $this->get(self::TAG_ROUTE . '?limit=10&sort=tag&order=desc&page=1');
        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_tag_returns_a_successful_response_with_search(): void
    {
        $this->markTestSkipped('Currently we dont use search. Maybe need later');
        $searchString = fake()->text(10);
        $response = $this->get(
            self::TAG_ROUTE . '?limit=10&sort=tag&order=desc&page=1&search=' . $searchString,
        );
        $response->assertStatus(200);
    }

    public function test_get_tag(): void
    {
        $response = $this->get(self::TAG_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_create_tag(): void
    {
        $response = $this->post(self::TAG_ROUTE, $this->getTestTagData());
        $response->assertStatus(200);
    }

    public function test_update_tag(): void
    {
        $response = $this->put(self::TAG_ROUTE . '/1', $this->getTestTagData());
        $response->assertStatus(200);
    }

    public function test_delete_tag(): void
    {
        $response = $this->delete(self::TAG_ROUTE . '/1');
        $response->assertStatus(200);

        $response = $this->get(self::TAG_ROUTE . '/1');
        $response->assertStatus(404);
    }

    private function getTestTagData(): array
    {
        return [
            'tag' => fake()->text(25),
            'backgroundColor' => fake()->rgbColor,
            'textColor' => fake()->rgbColor,
            'entityType' => 'Contact',
        ];
    }
}
