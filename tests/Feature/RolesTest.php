<?php

namespace Tests\Feature;

use Tests\TestCase;

class RolesTest extends TestCase
{
    public function test_the_roles_list(): void
    {
        $response = $this->get(self::ROLE_ROUTE . '?limit=10&order=desc&page=1');
        $response->assertStatus(200);
    }

    public function test_get_single_role(): void
    {
        $response = $this->get(self::ROLE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_store_role(): void
    {
        $response = $this->post(self::ROLE_ROUTE, ['name' => 'Test new role', 'description' => 'Test role']);
        $response->assertStatus(200);
    }

    public function test_update_role(): void
    {
        $response = $this->put(self::ROLE_ROUTE . '/2', ['description' => 'Test']);
        $response->assertStatus(200);
    }

    public function test_delete_role(): void
    {
        $response = $this->delete(self::ROLE_ROUTE . '/2');
        $response->assertStatus(200);

        $response = $this->get(self::ROLE_ROUTE . '/2');
        $response->assertStatus(404);
    }
}
