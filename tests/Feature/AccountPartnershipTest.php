<?php


use Tests\TestCase;

class AccountPartnershipTest extends TestCase
{
    public function test_account_partnership_list(): void
    {
        $response = $this->get(self::ACCOUNT_PARTNERSHIP_ROUTE);
        $response->assertStatus(200);
    }

    public function test_create_account_partnership(): void
    {
        $response = $this->post(self::ACCOUNT_PARTNERSHIP_ROUTE, $this->getAccountPartnershipData());
        $response->assertStatus(200);
    }

    public function test_single_account_partnership(): void
    {
        $response = $this->get(self::ACCOUNT_PARTNERSHIP_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_update_account_partnership(): void
    {
        $response = $this->put(self::ACCOUNT_PARTNERSHIP_ROUTE . '/1', $this->getAccountPartnershipData());
        $response->assertStatus(200);
    }

    public function test_delete_account_partnership(): void
    {
        $this->beginDatabaseTransaction();
        $response = $this->delete(self::ACCOUNT_PARTNERSHIP_ROUTE . '/1');
        $response->assertStatus(200);
    }

    private function getAccountPartnershipData(): array
    {
        return [
            'name' => fake()->text(20),
            'status' => 'Active',
        ];
    }
}
