<?php

namespace Tests\Feature;

use App\Models\Opportunity;
use App\Models\Tag;
use Tests\TestCase;

class OpportunityTest extends TestCase
{
    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_the_opportunity_returns_a_successful_response(string $input): void
    {
        $response = $this->get(self::OPPORTUNITY_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_the_opportunity_returns_a_successful_response_with_search(string $input): void
    {
        $this->markTestSkipped('Currently we dont use search. Maybe need later');
        $searchString = fake()->text(10);
        $response = $this->get(
            self::OPPORTUNITY_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1&search=' . $searchString,
        );
        $response->assertStatus(200);
    }

    public function test_get_opportunity(): void
    {
        $response = $this->get(self::OPPORTUNITY_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_create_opportunity(): void
    {
        $response = $this->post(self::OPPORTUNITY_ROUTE, $this->getTestData());
        $response->assertStatus(200);
    }

    public function test_update_opportunity(): void
    {
        $response = $this->put(self::OPPORTUNITY_ROUTE . '/1', $this->getTestData());
        $response->assertStatus(200);
    }

    public function test_delete_opportunity(): void
    {
        $response = $this->delete(self::OPPORTUNITY_ROUTE . '/1');
        $response->assertStatus(200);
        $response = $this->get(self::OPPORTUNITY_ROUTE . '/1');
        $response->assertStatus(404);
    }

    public function test_delete_bulk_opportunity(): void
    {
        $response = $this->delete(self::OPPORTUNITY_ROUTE . '/bulk', ['ids' => '2,3']);
        $response->assertStatus(200);
        $response = $this->get(self::OPPORTUNITY_ROUTE . '/2');
        $response->assertStatus(404);
    }

    private function getTestData(): array
    {
        return [
            'stageId' => 1,
            'accountId' => 1,
            'customFields' => [
                'opportunity-owner' => 1,
                'description' => 'test description',
            ],
            'tag' => [
                [
                    'id' => Tag::query()->where('entity_type', Opportunity::class)->first()->getKey(),
                ],
            ],

        ];
    }

    public static function getSortingList(): array
    {
        return [
            ['status'],
            ['probability',],
            ['created_at',],
            ['next-step',],
        ];
    }

    public function test_store_opportunity_attachment(): void
    {
        $response = $this->post(self::OPPORTUNITY_ROUTE . '/1/attachment', $this->getAttachmentData());
        $response->assertStatus(200);

        $this->flushHeaders();
        $response = $this->post(self::OPPORTUNITY_ROUTE . '/1/attachment', $this->getAttachmentData());
        $response->assertUnauthorized();
    }

    public function test_update_opportunity_attachment(): void
    {
        $response = $this->put(self::OPPORTUNITY_ROUTE . '/1/attachment/1', $this->getAttachmentData());
        $response->assertStatus(200);
    }

    public function test_delete_opportunity_attachment(): void
    {
        $response = $this->delete(self::OPPORTUNITY_ROUTE . '/1/attachment/2');
        $response->assertStatus(200);
    }

    public function getAttachmentData(): array
    {
        return [
            'link' => 'https://test',
        ];
    }
}
