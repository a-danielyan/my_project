<?php


use Tests\TestCase;

class ContactTypeTest extends TestCase
{
    public function test_contact_type_list(): void
    {
        $response = $this->get(self::CONTACT_TYPE_ROUTE);
        $response->assertStatus(200);
    }

    public function test_create_contact_type(): void
    {
        $response = $this->post(self::CONTACT_TYPE_ROUTE, $this->getAccountPartnershipData());
        $response->assertStatus(200);
    }

    public function test_single_contact_type(): void
    {
        $response = $this->get(self::CONTACT_TYPE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_update_contact_type(): void
    {
        $response = $this->put(self::CONTACT_TYPE_ROUTE . '/1', $this->getAccountPartnershipData());
        $response->assertStatus(200);
    }

    public function test_delete_contact_type(): void
    {
        $this->beginDatabaseTransaction();
        $response = $this->delete(self::CONTACT_TYPE_ROUTE . '/1');
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
