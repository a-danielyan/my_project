<?php

namespace Tests\Feature;

use App\Models\SalesTax;
use Tests\TestCase;

class SalesTaxTest extends TestCase
{
    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_sales_tax_returns_a_successful_response(string $input): void
    {
        $response = $this->get(self::SALES_TAX_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    /**
     * @dataProvider getFilterList
     */
    public function test_sales_tax_with_filter(string $filter, string $value): void
    {
        $response = $this->get(self::SALES_TAX_ROUTE . '?' . $filter . '=' . $value);
        $response->assertStatus(200);
    }

    public function test_get_sales_tax(): void
    {
        $response = $this->get(self::SALES_TAX_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_create_sales_tax(): void
    {
        $response = $this->post(self::SALES_TAX_ROUTE, $this->getTestData());
        $response->assertStatus(200)->assertJsonStructure(['id', 'stateCode', 'tax']);
        $createdEntity = json_decode($response->getContent());
        $response = $this->get(self::SALES_TAX_ROUTE . '/' . $createdEntity->id);
        $response->assertStatus(200);
    }

    public function test_update_sales_tax(): void
    {
        $response = $this->put(self::SALES_TAX_ROUTE . '/1', $this->getTestData());
        $response->assertStatus(200)->assertJsonStructure(['id', 'stateCode', 'tax']);
    }

    public function test_delete_sales_tax(): void
    {
        $response = $this->delete(self::SALES_TAX_ROUTE . '/1');
        $response->assertStatus(200);
        $response = $this->get(self::SALES_TAX_ROUTE . '/1');
        $response->assertStatus(404);
    }

    private function getTestData(): array
    {
        return [
            'stateCode' => 'CO',
            'tax' => fake()->randomFloat(2, 0, 99),
        ];
    }

    public static function getSortingList(): array
    {
        return [
            ['stateCode'],
        ];
    }

    public static function getFilterList(): array
    {
        return [
            ['stateCode', fake()->randomElement(SalesTax::AVAILABLE_STATE_CODES)],
        ];
    }
}
