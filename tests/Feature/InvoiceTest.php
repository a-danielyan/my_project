<?php

use App\Http\Services\StripeService;
use App\Models\Invoice;
use App\Models\Opportunity;
use App\Models\Product;
use Mockery\MockInterface;
use Stripe\Quote;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(
            StripeService::class,
            function (MockInterface $mock) {
                $stripeProduct = new \Stripe\Product('4748');
                $stripeInvoice = new \Stripe\Invoice('123456');
                $stripePrice = new \Stripe\Price('123');
                $stripeQuote = new Quote('test');
                $mock->shouldReceive('createQuote')->andReturn($stripeQuote);
                $mock->shouldReceive('updateQuote');
                $mock->shouldReceive('finalizeQuote');
                $mock->shouldReceive('acceptQuote');
                $mock->shouldReceive('finalizeInvoice');
                $mock->shouldReceive('createCustomer');
                $mock->shouldReceive('createProduct')->andReturn($stripeProduct);
                $mock->shouldReceive('getInvoice');
                $mock->shouldReceive('createSubscription');
                $mock->shouldReceive('createInvoiceItem');
                $mock->shouldReceive('createPrice')->andReturn($stripePrice);
                $mock->shouldReceive('createInvoice')->andReturn($stripeInvoice);
            },
        );
    }

    /**
     * @dataProvider getSortingList
     * @param string $input
     * @return void
     */
    public function test_the_invoice_returns_a_successful_response(string $input): void
    {
        $response = $this->get(self::INVOICE_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    public function test_get_invoice(): void
    {
        $response = $this->get(self::INVOICE_ROUTE . '/1');
        $response->assertStatus(200);
    }


    public function test_update_invoice(): void
    {
        $response = $this->put(self::INVOICE_ROUTE . '/1', $this->getTestInvoiceData());


        $response->assertStatus(200)->assertJsonStructure([
            "id",
            "invoiceNumber",
            "opportunityId",
            "estimateId",
            "estimateName",
        ]);
    }

    public function test_create_invoice(): void
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


        $response = $this->post(self::INVOICE_ROUTE, $this->getTestCreateInvoiceData($itemGroup));
        $response->assertStatus(200)->assertJsonStructure([
            "id",
            "invoiceNumber",
            "opportunityId",
            "estimateId",
            "estimateName",
        ]);
    }


    public function test_store_invoice_attachment(): void
    {
        $response = $this->post(self::INVOICE_ROUTE . '/1/attachment', $this->getAttachmentData());
        $response->assertStatus(200);

        $this->flushHeaders();
        $response = $this->post(self::INVOICE_ROUTE . '/1/attachment', $this->getAttachmentData());
        $response->assertUnauthorized();
    }

    public function test_update_invoice_attachment(): void
    {
        $response = $this->put(self::INVOICE_ROUTE . '/1/attachment/1', $this->getAttachmentData());
        $response->assertStatus(200);
    }

    public function test_delete_invoice_attachment(): void
    {
        $response = $this->delete(self::INVOICE_ROUTE . '/1/attachment/2');
        $response->assertStatus(200);
    }

    private function getTestInvoiceData(): array
    {
        return [
            "accountId" => 1,
            "dueDate" => "2023-01-02",
            "status" => "Open",
            "clientPO" => "1234",
            "parentPO" => "7788",
            "previousPO" => "147",
            "termsAndConditions" => "test1",
            "paymentTerm" => Invoice::PAYMENT_TERM_PREPAID,
            "notes" => "Test note",
            "ownerId" => 1,
            "orderType" => "New Business",
            "shipDate" => "2023-02-02",
            "shipCarrier" => "UPS",
            "shipInstruction" => "Basic instructions",
            "trackCodeStandard" => "RA12345",
            "trackCodeSpecial" => "UA789458",
            "shipCost" => 123.2,
            "cancelReason" => "I dont like it",
            "cancelDetails" => "Detailed cancel explain",
            "canceledBy" => 1,
            "refundAmount" => 50.5,
            "refundDate" => "2023-03-02",
            "refundReason" => "Sample reason",
            "refundedBy" => 1,
            'balanceDue' => fake()->randomFloat(2, 1, 500),
        ];
    }

    public static function getSortingList(): array
    {
        return [
            ['status'],
        ];
    }

    public function getAttachmentData(): array
    {
        return [
            'link' => 'https://test',
        ];
    }

    public function getTestCreateInvoiceData(
        array $itemGroup,
        $accountId = 1,
        $contactId = 1,
        array $additionalData = [],
    ): array {
        return array_merge([
            'accountId' => $accountId,
            'contactId' => $contactId,
            'paymentTerm' => Invoice::PAYMENT_TERM_PREPAID,
            'dueDate' => '2023-01-02',
            'termsAndConditions' => 'test1',
            'status' => 'Open',
            'notes' => 'Test note',
            'ownerId' => 1,
            'orderType' => Opportunity::EXISTED_BUSINESS,
            'itemGroups' => $itemGroup,
        ], $additionalData);
    }

    /**
     * @dataProvider getInvoiceProductData
     * @param array $productSet
     * @param float $totalSum
     * @return void
     */
    public function test_invoice_tax_calculation_response(array $productSet, float $totalSum): void
    {
        $response = $this->post(self::INVOICE_ROUTE, $productSet);
        $response->assertStatus(200);
        $subtotal = $response->json('grandTotal');
        $this->assertEquals($subtotal, $totalSum);
    }

    public function getInvoiceProductData(): array
    {
        return [
            [
                $this->getTestCreateInvoiceData([
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
                $this->getTestCreateInvoiceData([
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
                $this->getTestCreateInvoiceData([
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
                $this->getTestCreateInvoiceData([
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
            [
                $this->getTestCreateInvoiceData([
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
                $this->getTestCreateInvoiceData([
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
//Now with taxes
            [
                $this->getTestCreateInvoiceData([
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
                $this->getTestCreateInvoiceData([
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
                $this->getTestCreateInvoiceData([
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
