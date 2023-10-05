<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Tag;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_the_product_returns_a_successful_response(string $input): void
    {
        $response = $this->get(self::PRODUCT_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_the_product_returns_a_successful_response_with_search(string $input): void
    {
        $this->markTestSkipped('Currently we dont use search. Maybe need later');
        $searchString = fake()->text(10);
        $response = $this->get(
            self::PRODUCT_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1&search=' . $searchString,
        );
        $response->assertStatus(200);
    }

    public function test_get_product(): void
    {
        $response = $this->get(self::PRODUCT_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_create_product(): void
    {
        $response = $this->post(self::PRODUCT_ROUTE, $this->getTestProductData());
        $response->assertStatus(200);
    }

    public function test_update_product(): void
    {
        $response = $this->put(self::PRODUCT_ROUTE . '/1', $this->getTestProductData());
        $response->assertStatus(200);
    }

    public function test_delete_product(): void
    {
        $response = $this->delete(self::PRODUCT_ROUTE . '/1');
        $response->assertStatus(200);
        $response = $this->get(self::PRODUCT_ROUTE . '/1');
        $response->assertStatus(404);
    }

    public function test_delete_bulk_product(): void
    {
        $response = $this->delete(self::PRODUCT_ROUTE . '/bulk', ['ids' => '2,3']);
        $response->assertStatus(200);
        $response = $this->get(self::PRODUCT_ROUTE . '/2');
        $response->assertStatus(404);
    }


    public function test_restore_product(): void
    {
        $response = $this->get(self::PRODUCT_ROUTE . '/20');
        $response->assertStatus(404);
        $response = $this->post(self::PRODUCT_ROUTE . '/20/restore');
        $response->assertStatus(200);
        $response = $this->get(self::PRODUCT_ROUTE . '/20');
        $response->assertStatus(200);
    }

    public function test_store_account_attachment(): void
    {
        $response = $this->post(self::PRODUCT_ROUTE . '/1/attachment', $this->getAttachmentData());
        $response->assertStatus(200);

        $this->flushHeaders();
        $response = $this->post(self::PRODUCT_ROUTE . '/1/attachment', $this->getAttachmentData());
        $response->assertUnauthorized();
    }

    public function test_update_account_attachment(): void
    {
        $response = $this->put(self::PRODUCT_ROUTE . '/1/attachment/1', $this->getAttachmentData());
        $response->assertStatus(200);
    }

    public function test_delete_account_attachment(): void
    {
        $response = $this->delete(self::PRODUCT_ROUTE . '/1/attachment/2');
        $response->assertStatus(200);
    }


    private function getTestProductData(): array
    {
        return [
            'customFields' => [
                'product-code' => 'testing1',
            ],
            'tag' => [
                [
                    'id' => Tag::query()->where('entity_type', Product::class)->first()->getKey(),
                ],
            ],
        ];
    }

    public static function getSortingList(): array
    {
        return [
            ['status'],
            ['product-name',],
            ['created_at',],
            ['product-code',],
        ];
    }

    public function getAttachmentData(): array
    {
        return [
            'link' => 'https://test',
        ];
    }
}
