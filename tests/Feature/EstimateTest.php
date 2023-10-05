<?php

namespace Tests\Feature;

use App\Http\Services\StripeService;
use App\Models\Estimate;
use App\Models\Product;
use Mockery\MockInterface;
use Stripe\Invoice;
use Stripe\Quote;
use Stripe\Subscription;
use Tests\TestCase;

class EstimateTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        $stripeSubscription = new Subscription('123');
        $stripeSubscription->latest_invoice = '11';
        $stripeSubscription->ended_at = now();
        $this->mock(
            StripeService::class,
            function (MockInterface $mock) use ($stripeSubscription) {
                $stripeInvoice = new Invoice('123456');
                $stripeQuote = new Quote('test');
                $mock->shouldReceive('createQuote')->andReturn($stripeQuote);
                $mock->shouldReceive('updateQuote');
                $mock->shouldReceive('finalizeQuote');
                $mock->shouldReceive('acceptQuote');
                $mock->shouldReceive('finalizeInvoice');
                $mock->shouldReceive('createSubscription')->andReturn($stripeSubscription);
                $mock->shouldReceive('createInvoice')->andReturn($stripeInvoice);
            },
        );
    }

    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_estimate_returns_a_successful_response(string $input): void
    {
        $response = $this->get(self::ESTIMATE_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_estimate_returns_a_successful_response_with_search(string $input): void
    {
        $this->markTestSkipped('Currently we dont use search. Maybe need later');
        $searchString = fake()->text(10);
        $response = $this->get(
            self::ESTIMATE_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1&search=' . $searchString,
        );
        $response->assertStatus(200);
    }

    public function test_get_estimate(): void
    {
        $response = $this->get(self::ESTIMATE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_create_estimate(): void
    {
        $product = Product::query()->first();
        $itemGroup = [
            [
                'address' => [
                    'city' => 'Test',
                    'anyOtherData' => '11',
                ],
                'items' => [
                    [
                        'productId' => $product->getKey(),
                        'quantity' => 1,
                        'discount' => 1,
                        'description' => 'test description',
                    ],
                ],
            ],
        ];
        $response = $this->post(self::ESTIMATE_ROUTE, $this->getTestEstimateData($itemGroup));
        $response->assertStatus(200);
        //@todo check with opportunity without contact
    }

    public function test_update_estimate(): void
    {
        $product = Product::query()->first();
        $itemGroup = [
            [
                'address' => [
                    'city' => 'Test',
                    'anyOtherData' => '11',
                ],
                'items' => [
                    [
                        'productId' => $product->getKey(),
                        'quantity' => 10,
                        'discount' => 5,
                        'description' => 'test description',
                    ],
                ],
            ],
        ];
        $response = $this->put(self::ESTIMATE_ROUTE . '/1', $this->getTestEstimateData($itemGroup));
        $response->assertStatus(200);
    }

    public function test_update_estimate_bulk(): void
    {
        $response = $this->put(self::ESTIMATE_ROUTE . '/bulk?ids=1,2', $this->getBulkUpdateData());
        $response->assertStatus(200);
    }

    public function test_preview_estimate(): void
    {
        $response = $this->post(self::ESTIMATE_ROUTE . '/1/preview');
        $response->assertStatus(200);
    }

    public function test_generate_pdf_estimate(): void
    {
        $response = $this->post(self::ESTIMATE_ROUTE . '/1/pdf');
        $response->assertStatus(200);
    }

    public function test_generate_invoice_estimate(): void
    {
        $response = $this->post(self::ESTIMATE_ROUTE . '/1/invoice');
        $response->assertStatus(200);
    }


    public function test_delete_estimate(): void
    {
        $response = $this->delete(self::ESTIMATE_ROUTE . '/1');
        $response->assertStatus(200);
        $response = $this->get(self::ESTIMATE_ROUTE . '/1');
        $response->assertStatus(404);
    }

    public function test_delete_bulk_estimate(): void
    {
        $response = $this->delete(self::ESTIMATE_ROUTE . '/bulk', ['ids' => '2,3']);
        $response->assertStatus(200);
        $response = $this->get(self::ESTIMATE_ROUTE . '/2');
        $response->assertStatus(404);
    }

    public function test_store_contact_attachment(): void
    {
        $response = $this->post(self::ESTIMATE_ROUTE . '/1/attachment', $this->getAttachmentData());
        $response->assertStatus(200);

        $this->flushHeaders();
        $response = $this->post(self::ESTIMATE_ROUTE . '/1/attachment', $this->getAttachmentData());
        $response->assertUnauthorized();
    }

    public function test_update_contact_attachment(): void
    {
        $response = $this->put(self::ESTIMATE_ROUTE . '/1/attachment/1', $this->getAttachmentData());
        $response->assertStatus(200);
    }

    public function test_delete_contact_attachment(): void
    {
        $response = $this->delete(self::ESTIMATE_ROUTE . '/1/attachment/2');
        $response->assertStatus(200);
    }


    public function test_estimate_accepted()
    {
        // $response = $this->get(self::ESTIMATE_ROUTE . '/4');
        // $response->assertStatus(200);
        // $data = json_decode($response->getContent());

        $response = $this->put(self::ESTIMATE_ROUTE . '/4', ['status' => Estimate::ESTIMATE_STATUS_ACCEPTED]);

        $response->assertStatus(200);

        // $opportunityId = $data->customFields->opportunity->id;
        //  $closedWonStage = Stage::query()->where('name', Stage::CLOSED_WON_STAGE)->first();
        //  $this->assertDatabaseHas('opportunity',['id'=>$opportunityId,'stage_id'=>$closedWonStage->getKey()]);


        // $response = $this->get(self::OPPORTUNITY_ROUTE . '/' . $opportunityId);
        // $response->assertStatus(200);
        // $this->assertTrue($response['stage']['name'] === Stage::CLOSED_WON_STAGE); // data wrapped in transaction not returned
    }

    private function getTestEstimateData(
        array $itemGroup,
        $accountId = 1,
        $contactId = 1,
        array $additionalData = [],
    ): array {
        return array_merge([
            'customFields' => [
                'subject' => 'testing1',
            ],
            'itemGroups' => $itemGroup,
            "opportunityId" => 1,
            "accountId" => $accountId,
            "contactId" => $contactId,
        ], $additionalData);
    }

    public static function getSortingList(): array
    {
        return [
            ['subject'],
            ['status'],
            ['created_at'],
            ['billing-street'],
            ['estimateName'],
        ];
    }

    public function getAttachmentData(): array
    {
        return [
            'link' => 'https://test',
        ];
    }

    private function getBulkUpdateData(): array
    {
        return [
            "customFields" => [
                "subject" => "test subject bulk",
            ],
        ];
    }


    /**
     * @dataProvider getEstimateProductData
     * @param array $productSet
     * @param float $totalSum
     * @return void
     */
    public function test_estimate_tax_calculation_response(array $productSet, float $totalSum): void
    {
        $response = $this->post(self::ESTIMATE_ROUTE, $productSet);
        $response->assertStatus(200);
        $subtotal = $response->json('grandTotal');
        $this->assertEquals($subtotal, $totalSum);
    }

    public function getEstimateProductData(): array
    {
        return [
            [
                $this->getTestEstimateData([
                    [
                        'address' => [
                            'city' => 'Test',
                            'anyOtherData' => '11',

                        ],
                        'items' => [
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_10,
                                'quantity' => 1,
                                'discount' => 0,
                                'description' => 'test description',
                            ],
                        ],
                    ],
                ]),
                10,
            ],
            [
                $this->getTestEstimateData([
                    [
                        'address' => [
                            'city' => 'Test',
                            'anyOtherData' => '11',
                        ],
                        'items' => [
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_20,
                                'quantity' => 1,
                                'discount' => 0,
                                'description' => 'test description',
                            ],
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_10,
                                'quantity' => 2,
                                'discount' => 0,
                                'description' => 'test description',
                            ],
                        ],
                    ],
                ]),
                40,
            ],
            [
                $this->getTestEstimateData([
                    [
                        'address' => [
                            'city' => 'Test',
                            'anyOtherData' => '11',
                        ],
                        'items' => [
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_20,
                                'quantity' => 1,
                                'discount' => 0,
                                'description' => 'test description',
                            ],
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_10,
                                'quantity' => 2,
                                'discount' => 0,
                                'description' => 'test description',
                            ],
                        ],
                    ],
                    [
                        'address' => [
                            'city' => 'Test',
                            'anyOtherData' => '11',
                        ],
                        'items' => [
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_10,
                                'quantity' => 2,
                                'discount' => 5,
                                'description' => 'test description',
                            ],
                        ],
                    ],

                ]),
                55,
            ],
//check summary discount
            [
                $this->getTestEstimateData([
                    [
                        'address' => [
                            'city' => 'Test',
                            'anyOtherData' => '11',
                        ],
                        'items' => [
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_20,
                                'quantity' => 1,
                                'discount' => 0,
                                'description' => 'test description',
                            ],
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_10,
                                'quantity' => 2,
                                'discount' => 0,
                                'description' => 'test description',
                            ],
                        ],
                    ],
                    [
                        'address' => [
                            'city' => 'Test',
                            'anyOtherData' => '11',
                        ],
                        'items' => [
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_10,
                                'quantity' => 2,
                                'discount' => 5,
                                'description' => 'test description',
                            ],
                        ],
                    ],

                ], additionalData: ['discountPercent' => 50]),
                27.5,
            ],

//Now with taxes
            [
                $this->getTestEstimateData([
                    [
                        'address' => [
                            'city' => 'Test',
                            'anyOtherData' => '11',
                        ],
                        'items' => [
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_10,
                                'quantity' => 1,
                                'discount' => 0,
                                'description' => 'test description',
                            ],
                        ],
                    ],
                ], 1, TestCase::CONTACT_WITH_TAXES_ID),
                13,
            ],
            [
                $this->getTestEstimateData([
                    [
                        'address' => [
                            'city' => 'Test',
                            'anyOtherData' => '11',
                        ],
                        'items' => [
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_10,
                                'quantity' => 2,
                                'discount' => 5,
                                'description' => 'test description',
                            ],
                        ],
                    ],
                ], 1, TestCase::CONTACT_WITH_TAXES_ID),
                21,
            ],
            [
                $this->getTestEstimateData([
                    [
                        'address' => [
                            'city' => 'Test',
                            'anyOtherData' => '11',

                        ],
                        'items' => [
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_10,
                                'quantity' => 2,
                                'discount' => 5,
                                'description' => 'test description',
                            ],
                        ],
                    ],
                ], 1, TestCase::CONTACT_WITH_TAXES_ID, additionalData: ['discountPercent' => 50]),
                13.5,
            ],
            [
                $this->getTestEstimateData([
                    [
                        'address' => [
                            'city' => 'Test',
                            'anyOtherData' => '11',
                        ],
                        'items' => [
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_20,
                                'quantity' => 1,
                                'discount' => 0,
                                'description' => 'test description',
                            ],
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_10,
                                'quantity' => 2,
                                'discount' => 0,
                                'description' => 'test description',
                            ],
                        ],
                    ],
                ], 1, TestCase::CONTACT_WITH_TAXES_ID),
                52,
            ],
            [
                $this->getTestEstimateData([
                    [
                        'address' => [
                            'city' => 'Test',
                            'anyOtherData' => '11',
                        ],
                        'items' => [
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_20,
                                'quantity' => 1,
                                'discount' => 0,
                                'description' => 'test description',
                                'taxPercent' => 5,
                            ],
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_10,
                                'quantity' => 2,
                                'discount' => 0,
                                'description' => 'test description',
                                'taxPercent' => 5,
                            ],
                        ],
                    ],
                    [
                        'address' => [
                            'city' => 'Test',
                            'anyOtherData' => '11',
                        ],
                        'items' => [
                            [
                                'productId' => TestCase::PRODUCT_ID_WITH_PRICE_10,
                                'quantity' => 2,
                                'discount' => 5,
                                'description' => 'test description',
                            ],
                        ],
                    ],

                ], 1, TestCase::CONTACT_WITH_TAXES_ID),
                57,
            ],
        ];
    }

}
