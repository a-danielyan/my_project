<?php


use App\Console\Commands\AddAccountsToStripe;
use App\Console\Commands\AddProductsToStripe;
use App\Console\Commands\ClearZohoRawEventsTable;
use App\Console\Commands\UpdateInvoiceStatus;
use App\Http\Services\StripeService;
use App\Models\Invoice;
use Mockery\MockInterface;
use Tests\TestCase;

class ClearZohoRawEventsTableTest extends TestCase
{
    /**
     * @return void
     */
    public function test_add_account_to_stripe(): void
    {
        $this->artisan(ClearZohoRawEventsTable::class)->assertSuccessful();
    }
}
