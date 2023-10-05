<?php

namespace App\Jobs;

use App\DTO\StripeProductDTO;
use App\Helpers\CustomFieldValuesHelper;
use App\Http\Repositories\ProductRepository;
use App\Http\Services\StripeService;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

class CreateStripeProduct implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private ProductRepository $productRepository;

    /**
     * Create a new job instance.
     */
    public function __construct(private Product $product)
    {
        $this->productRepository = resolve(ProductRepository::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $customFieldValues = CustomFieldValuesHelper::getCustomFieldValues(
            $this->product,
            ['product-name', 'product-description'],
        );
        $product = new StripeProductDTO();
        $product->name = $customFieldValues['product-name'] ?? '';
        $product->description = $customFieldValues['product-description'] ?? '';

        /** @var StripeService $stripeService */
        $stripeService = resolve(StripeService::class);
        try {
            $stripeProduct = $stripeService->createProduct($product);

            $this->productRepository->update($this->product, ['stripe_product_id' => $stripeProduct->id]);
        } catch (ApiErrorException $e) {
            Log::error('Cant create product', [
                'productId' => $this->product->getKey(),
                'productData' => $product->toStripeArray(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
