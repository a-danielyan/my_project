<?php

namespace Tests\Feature;

use Tests\TestCase;

class CustomFieldTest extends TestCase
{
    /**
     * @dataProvider getSortingList
     * @return void
     */
    public function test_custom_field_list(string $input): void
    {
        $response = $this->get(self::CUSTOM_FIELD_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    public function test_create_custom_field(): void
    {
        $response = $this->post(self::CUSTOM_FIELD_ROUTE, $this->getCustomFieldData());
        $response->assertStatus(200);
    }

    public function test_single_custom_field(): void
    {
        $response = $this->get(self::CUSTOM_FIELD_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_update_custom_field(): void
    {
        $response = $this->put(self::CUSTOM_FIELD_ROUTE . '/1', $this->getCustomFieldData());
        $response->assertStatus(200);
    }

    public function test_bulk_update_custom_field(): void
    {
        $response = $this->post(self::CUSTOM_FIELD_ROUTE . '/bulk', $this->getCustomFieldBulkData());
        $response->assertStatus(200);
    }

    public function test_custom_field_settings(): void
    {
        $response = $this->get(self::CUSTOM_FIELD_ROUTE . 's/settings');
        $response->assertStatus(200)->assertJsonStructure(['availableCustomFieldTypes', 'lookupType']);
    }

    public function test_delete_custom_field(): void
    {
        $this->beginDatabaseTransaction();
        $response = $this->delete(self::CUSTOM_FIELD_ROUTE . '/1');
        $response->assertStatus(200);
    }

    private function getCustomFieldData(): array
    {
        return [
            'entityType' => 'Lead',
            'name' => fake()->text(20),
            'type' => 'container',
        ];
    }

    private function getCustomFieldBulkData(): array
    {
        return [
            "entityType" => "Lead",
            "fields" => [
                [
                    "name" => "Manage users1",
                    "type" => "container",
                    "sortOrder" => 4,
                    "childs" => [
                        [
                            "id" => 25,
                            "name" => "Solution interest",
                            "type" => "lookup",
                            "lookupType" => "solution",
                            "sortOrder" => 1,
                            "isRequired" => false,
                            "isUnique" => false,
                        ],
                        [
                            "id" => 18,
                            "name" => "Website",
                            "type" => "text",
                            "lookupType" => null,
                            "sortOrder" => 2,
                            "isRequired" => false,
                            "isUnique" => false,
                        ],
                    ],
                ],
                [
                    "name" => "Manage users2",
                    "type" => "container",
                    "sortOrder" => 3,
                    "childs" => [
                    ],
                    "width" => 0.5,
                ],
                [
                    "id" => 17,
                    "name" => "Last name",
                    "type" => "text",
                    "lookupType" => null,
                    "sortOrder" => 4,
                    "isRequired" => false,
                    "isUnique" => false,
                    "width" => 2,
                ],
                [
                    "name" => "Test select",
                    "type" => "select",
                    "lookupType" => null,
                    "sortOrder" => 4,
                    "isRequired" => false,
                    "isUnique" => false,
                    "options" => [
                        "select1",
                        "select2",
                    ],
                    "width" => 1.5,
                    "tooltip" => "Test tooltip",
                    "tooltipType" => "text",
                    "property" => [
                        "decimal" => 2,
                        "var" => 1,
                    ],
                ],
            ],
        ];
    }

    public static function getSortingList(): array
    {
        return [
            ['entity_type'],
            ['code'],
            ['created_at'],
        ];
    }

}
