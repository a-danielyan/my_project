<?php


use App\Console\Commands\AddAccountsToStripe;
use App\Console\Commands\UpdateInvoiceStatus;
use App\Http\Services\StripeService;
use App\Models\Invoice;
use Mockery\MockInterface;
use Tests\TestCase;

class AddAccountsToStripeTest extends TestCase
{
    /**
     * @return void
     */
    public function test_add_account_to_stripe(): void
    {
        $this->mock(
            StripeService::class,
            function (MockInterface $mock) {
                $stripeCustomer = new \Stripe\Customer('111');
                $mock->shouldReceive('createCustomer')->andReturn($stripeCustomer);;
            },
        );

        $this->artisan(AddAccountsToStripe::class)->assertSuccessful();
    }
}
