<?php

namespace App\Traits;

use App\DTO\StripeProductDTO;
use App\Exceptions\CustomErrorException;
use App\Helpers\CustomFieldValuesHelper;
use App\Http\Repositories\ProductRepository;
use App\Http\Services\StripeService;
use App\Models\EstimateItem;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

trait GenerateStripeLineItemsArrayTrait
{
    /**
     * @return array
     * @throws CustomErrorException
     */
    private function generateLineItemsArray(): array
    {
        $estimateItemsGroup = $this->estimate->estimateItemGroup;

        $lineItems = [];
        foreach ($estimateItemsGroup as $groupItems) {
            foreach ($groupItems->items as $estimateProduct) {
                $lineItems[] = $this->generateLineItemData($estimateProduct);
            }
        }

        return $lineItems;
    }

    /**
     * @param EstimateItem|InvoiceItem $estimateProduct
     * @return array
     * @throws CustomErrorException
     */
    private function generateLineItemData(EstimateItem|InvoiceItem $estimateProduct): array
    {
        $product = $estimateProduct->product;
        if (empty($product->stripe_product_id)) {
            $customFieldValues = CustomFieldValuesHelper::getCustomFieldValues(
                $estimateProduct->product,
                ['product-name', 'product-description'],
            );
            $stripeProductDto = new StripeProductDTO();
            $stripeProductDto->name = $customFieldValues['product-name'] ?? '';
            $stripeProductDto->description = $customFieldValues['product-description'] ?? '';

            /** @var StripeService $stripeService */
            $stripeService = resolve(StripeService::class);
            try {
                $stripeProduct = $stripeService->createProduct($stripeProductDto);
                $productRepository = resolve(ProductRepository::class);
                $productRepository->update($estimateProduct->product, ['stripe_product_id' => $stripeProduct->id]);
            } catch (ApiErrorException $e) {
                Log::error('Cant create product', [
                    'productId' => $estimateProduct->product->getKey(),
                    'productData' => $stripeProductDto->toStripeArray(),
                    'error' => $e->getMessage(),
                ]);
                throw new CustomErrorException('Cant create product on stripe.', 422);
            }
        }

        $customFieldValues = CustomFieldValuesHelper::getCustomFieldValues(
            $product,
            ['product-recurring', 'recurring-frequency'],
        );

        $productPrice =  CustomFieldValuesHelper::getProductPriceValue($product);
        $productPriceData = [
            'price_data' => [
                'currency' => 'USD',
                'product' => $product->stripe_product_id,
                'unit_amount' => ($productPrice - $estimateProduct->discount) * 100,
            ],
            'quantity' => (int)$estimateProduct->quantity,
        ];

        if ($customFieldValues['product-recurring']) {
            switch ($customFieldValues['recurring-frequency']['name']) {
                case 'Monthly':
                    $productPriceData['price_data']['recurring'] = [
                        'interval' => 'month',
                        'interval_count' => 1,
                    ];
                    break;

                case 'Quarterly':
                    $productPriceData['price_data']['recurring'] = [
                        'interval' => 'month',
                        'interval_count' => 3,
                    ];
                    break;

                case 'Yearly':
                    $productPriceData['price_data']['recurring'] = [
                        'interval' => 'year',
                        'interval_count' => 1,
                    ];
                    break;
                case '2 Year':
                    $productPriceData['price_data']['recurring'] = [
                        'interval' => 'year',
                        'interval_count' => 2,
                    ];
                    break;

                default:
                    break;
            }
        }

        return $productPriceData;
    }
}
