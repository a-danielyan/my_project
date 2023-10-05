<?php

namespace App\Jobs;

use App\DTO\StripeCustomerDTO;
use App\Helpers\CustomFieldValuesHelper;
use App\Http\Repositories\AccountRepository;
use App\Http\Services\StripeService;
use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

class CreateStripeCustomer implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private AccountRepository $accountRepository;

    /**
     * Create a new job instance.
     */
    public function __construct(private Account $account)
    {
        $this->accountRepository = resolve(AccountRepository::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $customFieldValues = CustomFieldValuesHelper::getCustomFieldValues($this->account, ['account-name', 'phone']);

        $customer = new StripeCustomerDTO();
        $customer->name = $customFieldValues['account-name'] ?? '';
        $customer->phone = $customFieldValues['phone'] ?? '';

        /** @var StripeService $stripeService */
        $stripeService = resolve(StripeService::class);
        try {
            $stripeAccount = $stripeService->createCustomer($customer);

            $this->accountRepository->update($this->account, ['stripe_customer_id' => $stripeAccount->id]);
        } catch (ApiErrorException $e) {
            Log::error('Cant create stripe customer ', [
                'accountId' => $this->account->getKey(),
                'customerData' => $customer->toStripeArray(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
