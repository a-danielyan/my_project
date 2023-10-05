<?php

namespace App\Console\Commands;

use App\Jobs\CreateStripeProduct;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class AddProductsToStripe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-products-to-stripe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add products to stripe products';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Product::query()->whereNull('stripe_product_id')->chunk(10, function (Collection $items) {
            foreach ($items as $product) {
                CreateStripeProduct::dispatch($product);
            }
        });
    }
}
