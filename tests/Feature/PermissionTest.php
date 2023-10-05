<?php

namespace Tests\Feature;

use App\Models\CustomField;
use App\Models\Role;
use Database\Seeders\TestDatabaseSeeder;
use Tests\TestCase;
use Throwable;

class PermissionTest extends TestCase
{
    public function test_get_permission(): void
    {
        $response = $this->get(self::ROLE_ROUTE . '/1/permission');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            [
                'id',
                'name',
                'group',
                'action',
                'attached',
            ],
        ]);
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function test_assign_permission_to_role()
    {
        $this->authorize(TestDatabaseSeeder::TEST_CONSULTANT_USER_EMAIL);

        $response = $this->get(self::ROLE_ROUTE . '/1');
        $response->assertStatus(403);

        $this->authorize(TestDatabaseSeeder::TEST_ADMIN_USER_EMAIL);
        $response = $this->put(self::ROLE_ROUTE . '/2/permission', $this->getPermissionData());
        $response->assertStatus(200);

        $this->authorize(TestDatabaseSeeder::TEST_CONSULTANT_USER_EMAIL);

        $response = $this->get(self::ROLE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    private function getPermissionData(): array
    {
        $rolePermissions = CustomField::query()->where('entity_type', Role::class)->first()->permissions;

        return [
            'permissionIds' => $rolePermissions->pluck('id')->toArray(),
        ];
    }
}
