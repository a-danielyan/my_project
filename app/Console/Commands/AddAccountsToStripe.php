<?php

namespace App\Console\Commands;

use App\Jobs\CreateStripeCustomer;
use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class AddAccountsToStripe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-accounts-to-stripe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create customer based on account details';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Account::query()->whereNull('stripe_customer_id')->chunk(10, function (Collection $items) {
            foreach ($items as $account) {
                CreateStripeCustomer::dispatch($account);
            }
        });
    }
}
