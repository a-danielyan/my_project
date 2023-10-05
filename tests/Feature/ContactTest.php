<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Tag;
use Tests\TestCase;

class ContactTest extends TestCase
{
    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_the_contacts_returns_a_successful_response(string $input): void
    {
        $response = $this->get(self::CONTACT_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_the_contacts_returns_a_successful_response_with_search(string $input): void
    {
        $this->markTestSkipped('Currently we dont use search. Maybe need later');
        $searchString = fake()->text(10);
        $response = $this->get(
            self::CONTACT_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1&search=' . $searchString,
        );
        $response->assertStatus(200);
    }

    public function test_get_contact(): void
    {
        $response = $this->get(self::CONTACT_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_create_contact(): void
    {
        $response = $this->post(self::CONTACT_ROUTE, $this->getTestContactData());
        $response->assertStatus(200);
    }

    public function test_update_contact(): void
    {
        $response = $this->put(self::CONTACT_ROUTE . '/1', $this->getTestContactData());
        $response->assertStatus(200);
    }

    public function test_bulk_update_contact(): void
    {
        $response = $this->put(self::CONTACT_ROUTE . '/bulk?ids=1,2', $this->getBulkUpdateData());
        $response->assertStatus(200);
    }


    public function test_delete_contact(): void
    {
        $response = $this->delete(self::CONTACT_ROUTE . '/2');
        $response->assertStatus(200);
        $response = $this->get(self::CONTACT_ROUTE . '/2');
        $response->assertStatus(404);
    }


    public function test_store_contact_attachment(): void
    {
        $response = $this->post(self::CONTACT_ROUTE . '/1/attachment', $this->getAttachmentData());
        $response->assertStatus(200);

        $this->flushHeaders();
        $response = $this->post(self::CONTACT_ROUTE . '/1/attachment', $this->getAttachmentData());
        $response->assertUnauthorized();
    }

    public function test_update_contact_attachment(): void
    {
        $response = $this->put(self::CONTACT_ROUTE . '/1/attachment/1', $this->getAttachmentData());
        $response->assertStatus(200);
    }

    public function test_delete_contact_attachment(): void
    {
        $response = $this->delete(self::CONTACT_ROUTE . '/1/attachment/2');
        $response->assertStatus(200);
    }

    public function test_delete_bulk_contact(): void
    {
        $response = $this->delete(self::CONTACT_ROUTE . '/bulk', ['ids' => '2,3']);
        $response->assertStatus(200);
        $response = $this->get(self::CONTACT_ROUTE . '/2');
        $response->assertStatus(404);
    }


    public function test_restore_contact(): void
    {
        $response = $this->get(self::CONTACT_ROUTE . '/20');
        $response->assertStatus(404);
        $response = $this->post(self::CONTACT_ROUTE . '/20/restore');
        $response->assertStatus(200);
        $response = $this->get(self::CONTACT_ROUTE . '/20');
        $response->assertStatus(200);
    }


    private function getTestContactData(): array
    {
        return [

            'salutation' => 'Mr.',
            'accountId' => 1,
            'customFields' => [
                'email' => 'testing1@test.com',
                'lead-source' => 1,
                'lead-description' => 'test description',
            ],
            'tag' => [
                [
                    'id' => Tag::query()->where('entity_type', Contact::class)->first()->getKey(),
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

    private function getBulkUpdateData(): array
    {
        return [
            "customFields" => [
                "lead-description" => "test description bulk",
            ],
        ];
    }
}
