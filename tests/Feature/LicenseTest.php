<?php

namespace Tests\Feature;

use App\Models\License;
use Tests\TestCase;

class LicenseTest extends TestCase
{
    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_the_license_returns_a_successful_response(string $input): void
    {
        $response = $this->get(self::LICENSE_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_the_license_returns_a_successful_response_with_search(string $input): void
    {
        $this->markTestSkipped('Currently we dont use search. Maybe need later');
        $searchString = fake()->text(10);
        $response = $this->get(
            self::LICENSE_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1&search=' . $searchString,
        );
        $response->assertStatus(200);
    }

    public function test_get_license(): void
    {
        $response = $this->get(self::LICENSE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_create_license(): void
    {
        $response = $this->post(self::LICENSE_ROUTE, $this->getTestLicenseData());
        $response->assertStatus(200);
    }

    public function test_update_license(): void
    {
        $response = $this->put(self::LICENSE_ROUTE . '/1', $this->getTestLicenseData());
        $response->assertStatus(200);
    }

    public function test_delete_license(): void
    {
        $response = $this->delete(self::LICENSE_ROUTE . '/1', $this->getTestLicenseData());
        $response->assertStatus(200);

        $response = $this->get(self::LICENSE_ROUTE . '/1');
        $response->assertStatus(404);
    }

    private function getTestLicenseData(): array
    {
        return [

            'name' => fake()->text(25),
            'licenseType' => fake()->randomElement(License::AVAILABLE_LICENSE_TYPES),
            'licenseDurationInMonth' => fake()->numberBetween(1, 10),
        ];
    }

    public static function getSortingList(): array
    {
        return [
            ['name'],
            ['status'],
            ['created_at'],
            ['licenseType'],
            ['licenseDurationInMonth'],
        ];
    }
}
