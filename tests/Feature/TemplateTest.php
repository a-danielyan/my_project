<?php
namespace Tests\Feature;

use App\Models\Template;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_templates_returns_a_successful_response(string $input): void
    {
        $response = $this->get(self::TEMPLATE_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    /**
     * @dataProvider getFilterList
     */
    public function test_templates_with_filter(string $filter, string $value): void
    {
        $response = $this->get(self::TEMPLATE_ROUTE . '?' . $filter . '='.$value);
        $response->assertStatus(200);
    }

    public function test_get_template(): void
    {
        $response = $this->get(self::TEMPLATE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_create_template(): void
    {
        $response = $this->post(self::TEMPLATE_ROUTE, $this->getTestData());
        $response->assertStatus(200)->assertJsonStructure(['id', 'entity', 'template']);
        $createdAccount = json_decode($response->getContent());
        $response = $this->get(self::TEMPLATE_ROUTE . '/' . $createdAccount->id);
        $response->assertStatus(200);
    }

    public function test_update_template(): void
    {
        $response = $this->put(self::TEMPLATE_ROUTE.'/1', $this->getTestData());
        $response->assertStatus(200)->assertJsonStructure(['id', 'entity', 'template']);
    }

    public function test_delete_template(): void
    {
        $response = $this->delete(self::TEMPLATE_ROUTE . '/1');
        $response->assertStatus(200);
        $response = $this->get(self::TEMPLATE_ROUTE . '/1');
        $response->assertStatus(404);
    }

    private function getTestData(): array
    {
        return [
            'entity' => Template::TEMPLATE_TYPE_EMAIL,
            'template' => fake()->randomHtml,
            ];
    }

    public static function getSortingList(): array
    {
        return [
            ['entity'],
        ];
    }

    public static function getFilterList(): array
    {
        return [
            ['entity',Template::TEMPLATE_TYPE_INVOICE],
        ];
    }

    public function getAttachmentData(): array
    {
        return [
            'link' => 'https://test',
        ];
    }
}
