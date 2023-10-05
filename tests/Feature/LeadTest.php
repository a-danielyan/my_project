<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Tag;
use Tests\TestCase;

class LeadTest extends TestCase
{
    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_the_leads_returns_a_successful_response(string $input): void
    {
        $response = $this->get(self::LEAD_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_the_leads_returns_a_successful_response_with_search(string $input): void
    {
        $this->markTestSkipped('Currently we dont use search. Maybe need later');
        $searchString = fake()->text(10);
        $response = $this->get(
            self::LEAD_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1&search=' . $searchString,
        );
        $response->assertStatus(200);
    }

    public function test_get_lead(): void
    {
        $response = $this->get(self::LEAD_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_create_lead(): void
    {
        $response = $this->post(self::LEAD_ROUTE, $this->getTestLeadData());
        $response->assertStatus(200);
        //@todo return lead data. check DB structure
    }

    public function test_update_lead(): void
    {
        $response = $this->put(self::LEAD_ROUTE . '/1', $this->getTestLeadData());
        $response->assertStatus(200);
        //@todo return lead data. check DB structure
    }

    public function test_delete_lead(): void
    {
        $response = $this->delete(self::LEAD_ROUTE . '/1');
        $response->assertStatus(200);
        $response = $this->get(self::LEAD_ROUTE . '/1');
        $response->assertStatus(404);
    }

    public function test_delete_bulk_lead(): void
    {
        $response = $this->delete(self::LEAD_ROUTE . '/bulk', ['ids' => '2,3']);
        $response->assertStatus(200);
        $response = $this->get(self::LEAD_ROUTE . '/2');
        $response->assertStatus(404);
    }


    public function test_restore_lead(): void
    {
        $response = $this->get(self::LEAD_ROUTE . '/20');
        $response->assertStatus(404);
        $response = $this->post(self::LEAD_ROUTE . '/20/restore');
        $response->assertStatus(200);
        $response = $this->get(self::LEAD_ROUTE . '/20');
        $response->assertStatus(200);
    }

    public function test_store_lead_attachment(): void
    {
        $response = $this->post(self::LEAD_ROUTE . '/1/attachment', $this->getAttachmentData());
        $response->assertStatus(200);

        $this->flushHeaders();
        $response = $this->post(self::LEAD_ROUTE . '/1/attachment', $this->getAttachmentData());
        $response->assertUnauthorized();
    }

    public function test_update_lead_attachment(): void
    {
        $response = $this->put(self::LEAD_ROUTE . '/1/attachment/1', $this->getAttachmentData());
        $response->assertStatus(200);
    }

    public function test_delete_lead_attachment(): void
    {
        $response = $this->delete(self::LEAD_ROUTE . '/1/attachment/2');
        $response->assertStatus(200);
    }

    private function getTestLeadData(): array
    {
        return [

            'salutation' => 'Mr.',
            'customFields' => [
                'email' => 'testing1@test.com',
                'lead-source' => 1,
                'lead-description' => 'test description',
                'phone' => '123456789',
            ],
            'tag' => [
                [
                    'id' => Tag::query()->where('entity_type', Lead::class)->first()->getKey(),
                ],
            ],

        ];
    }

    public static function getSortingList(): array
    {
        return [
            ['email'],
            ['status'],
            ['created_at'],
            ['first-name'],
        ];
    }

    public function getAttachmentData(): array
    {
        return [
            'link' => 'https://test',
        ];
    }
}
