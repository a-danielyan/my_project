<?php

use App\Models\Account;
use App\Models\Tag;
use Tests\TestCase;

class AccountTest extends TestCase
{
    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_the_accounts_returns_a_successful_response(string $input): void
    {
        $response = $this->get(self::ACCOUNT_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    /**
     * @dataProvider getFilterList
     */
    public function test_the_accounts_with_filter(string $filter, string $value): void
    {
        $response = $this->get(self::ACCOUNT_ROUTE . '?' . $filter . '=' . $value);
        $response->assertStatus(200);
    }

    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_the_accounts_returns_a_successful_response_with_search(string $input): void
    {
        $this->markTestSkipped('Currently we dont use search. Maybe need later');
        $searchString = fake()->text(10);
        $response = $this->get(
            self::ACCOUNT_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1&search=' . $searchString,
        );
        $response->assertStatus(200);
    }

    public function test_get_account(): void
    {
        $response = $this->get(self::ACCOUNT_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_create_account(): void
    {
        $response = $this->post(self::ACCOUNT_ROUTE, $this->getTestAccountData());
        $response->assertStatus(200)->assertJsonStructure(['id', 'customFields']);
        $createdAccount = json_decode($response->getContent());
        $response = $this->get(self::ACCOUNT_ROUTE . '/' . $createdAccount->id);
        $response->assertStatus(200);
    }

    public function test_update_account(): void
    {
        $response = $this->put(self::ACCOUNT_ROUTE . '/1', $this->getTestAccountData());
        $response->assertStatus(200)->assertJsonStructure(['id', 'customFields']);
    }

    public function test_bulk_update_account(): void
    {
        $response = $this->put(self::ACCOUNT_ROUTE . '/bulk?ids=1,2', $this->getBulkUpdateData());
        $response->assertStatus(200);
    }


    public function test_delete_account(): void
    {
        $response = $this->delete(self::ACCOUNT_ROUTE . '/1');
        $response->assertStatus(200);
        $response = $this->get(self::ACCOUNT_ROUTE . '/1');
        $response->assertStatus(404);
    }

    public function test_delete_bulk_account(): void
    {
        $response = $this->delete(self::ACCOUNT_ROUTE . '/bulk', ['ids' => '2,3']);
        $response->assertStatus(200);
        $response = $this->get(self::ACCOUNT_ROUTE . '/2');
        $response->assertStatus(404);
    }

    public function test_restore_account(): void
    {
        $response = $this->get(self::ACCOUNT_ROUTE . '/20');
        $response->assertStatus(404);
        $response = $this->post(self::ACCOUNT_ROUTE . '/20/restore');
        $response->assertStatus(200);
        $response = $this->get(self::ACCOUNT_ROUTE . '/20');
        $response->assertStatus(200);
    }

    public function test_store_account_attachment(): void
    {
        $response = $this->post(self::ACCOUNT_ROUTE . '/1/attachment', $this->getAttachmentData());
        $response->assertStatus(200);

        $this->flushHeaders();
        $response = $this->post(self::ACCOUNT_ROUTE . '/1/attachment', $this->getAttachmentData());
        $response->assertUnauthorized();
    }

    public function test_update_account_attachment(): void
    {
        $response = $this->put(self::ACCOUNT_ROUTE . '/1/attachment/1', $this->getAttachmentData());
        $response->assertStatus(200);
    }

    public function test_delete_account_attachment(): void
    {
        $response = $this->delete(self::ACCOUNT_ROUTE . '/1/attachment/2');
        $response->assertStatus(200);
    }

    private function getTestAccountData(): array
    {
        return [
            'accountId' => 1,
            'customFields' => [
                'email' => 'testing1@test.com',
                'lead-source' => 1,
                'lead-description' => 'test description',
            ],
            'tag' => [
                [
                    'id' => Tag::query()->where('entity_type', Account::class)->first()->getKey(),
                ],
            ],

        ];
    }

    public static function getSortingList(): array
    {
        return [
            ['status'],
            ['account-name'],
            ['created_at'],
        ];
    }

    public static function getFilterList(): array
    {
        return [
            ['status', 'Active'],
            ['email', fake()->email],
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
