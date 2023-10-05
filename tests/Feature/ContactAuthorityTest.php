<?php


use Tests\TestCase;

class ContactAuthorityTest extends TestCase
{
    public function test_contact_authority_list(): void
    {
        $response = $this->get(self::CONTACT_AUTHORITY_ROUTE);
        $response->assertStatus(200);
    }

    public function test_create_contact_authority(): void
    {
        $response = $this->post(self::CONTACT_AUTHORITY_ROUTE, $this->getContactAuthorityData());
        $response->assertStatus(200);
    }

    public function test_single_contact_authority(): void
    {
        $response = $this->get(self::CONTACT_AUTHORITY_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_update_contact_authority(): void
    {
        $response = $this->put(self::CONTACT_AUTHORITY_ROUTE . '/1', $this->getContactAuthorityData());
        $response->assertStatus(200);
    }

    public function test_delete_contact_authority(): void
    {
        $this->beginDatabaseTransaction();
        $response = $this->delete(self::CONTACT_AUTHORITY_ROUTE . '/1');
        $response->assertStatus(200);
    }

    private function getContactAuthorityData(): array
    {
        return [
            'name' => fake()->text(20),
            'status' => 'Active',
        ];
    }
}
