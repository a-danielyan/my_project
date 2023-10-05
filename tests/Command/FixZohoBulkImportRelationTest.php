<?php


use App\Console\Commands\AddAccountsToStripe;
use App\Console\Commands\AddProductsToStripe;
use App\Console\Commands\ClearZohoRawEventsTable;
use App\Console\Commands\ConvertImportedZohoEntity;
use App\Console\Commands\CreateZohoBulkImport;
use App\Console\Commands\FixZohoBulkImportRelation;
use App\Console\Commands\UpdateInvoiceStatus;
use App\Http\Services\StripeService;
use App\Models\Invoice;
use Mockery\MockInterface;
use Tests\TestCase;

class FixZohoBulkImportRelationTest extends TestCase
{
    /**
     * @return void
     */
    public function test_add_account_to_stripe(): void
    {
        $this->artisan(FixZohoBulkImportRelation::class)->assertSuccessful();
    }
}
