<?php

namespace App\Jobs;

use App\Exceptions\CustomErrorException;
use App\Helpers\CommonHelper;
use App\Helpers\CustomFieldValuesHelper;
use App\Http\Repositories\SubscriptionRepository;
use App\Http\Services\StripeService;
use App\Models\EstimateItem;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Traits\GenerateStripeLineItemsArrayTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

class GenerateStripeInvoiceWithSubscription implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use GenerateStripeLineItemsArrayTrait;

    private SubscriptionRepository $subscriptionRepository;

    /**
     * Create a new job instance.
     */
    public function __construct(private Invoice $invoice)
    {
        $this->subscriptionRepository = resolve(SubscriptionRepository::class);
    }

    /**
     * @return void
     * @throws ApiErrorException
     * @throws CustomErrorException
     */
    public function handle(): void
    {
        $customerId = $this->invoice->account->stripe_customer_id;
        if (empty($customerId)) {
            Log::error('We cant create subscription. CustomerId missed', [
                'invoiceId' => $this->invoice->getKey(),
                'accountId' => $this->invoice->account->getKey(),
            ]);

            return;
        }


        /** @var StripeService $stripeService */
        $stripeService = resolve(StripeService::class);

        $subscriptionItems = [];
        $oneTimePaymentItems = [];

        foreach ($this->invoice->invoiceItem as $item) {
            /** @var EstimateItem $item */
            $product = $item->product;

            $allProductFields = CustomFieldValuesHelper::getCustomFieldValues($product, ['product-recurring']);

            if ($allProductFields['product-recurring']) {
                //then we add it to subscription
                $subscriptionItems[] = $this->generateLineItemData($item);
            } else {
                // add one time payment
                $oneTimePaymentItems[] = $this->generateLineItemData($item);
            }
        }

        foreach ($oneTimePaymentItems as $paymentItem) {
            try {
                $priceItem = $stripeService->createPrice($paymentItem['price_data']);
                $priceId = $priceItem->id;
            } catch (ApiErrorException $e) {
                Log::error('We cant create price element', [
                    'priceData' => $paymentItem['price_data'],
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }

            try {
                $stripeService->createInvoiceItem($customerId, $priceId);
            } catch (ApiErrorException $e) {
                Log::error('We cant create invoice item', [
                    'customerId' => $customerId,
                    'priceId' => $priceId,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        }
        $subscriptionEndedAt = null;
        $stripeSubscriptionId = null;
        if (!empty($subscriptionItems)) {
            try {
                $subscription = $stripeService->createSubscription($customerId, $subscriptionItems);
                $stripeInvoiceId = $subscription->latest_invoice;
                $subscriptionEndedAt = $subscription->ended_at;
                $stripeSubscriptionId = $subscription->id;
            } catch (ApiErrorException $e) {
                Log::error('We cant create subscription item', [
                    'customerId' => $customerId,
                    'subscriptionItems' => $subscriptionItems,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        } else {
            try {
                $stripeInvoice = $stripeService->createInvoice($customerId);
                $stripeInvoiceId = $stripeInvoice->id;
            } catch (ApiErrorException $e) {
                Log::error('We cant create invoice ', [
                    'customerId' => $customerId,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        }
        $this->invoice->stripe_invoice_id = $stripeInvoiceId;
        $this->invoice->save();
        $cronUser = CommonHelper::getCronUser();
        Subscription::query()->create([
            'created_by' => $cronUser->getKey(),
            'subscription_name' => 'SUB_' . $this->invoice->account->getKey() . '_' . date('Ymd H:i:s'),
            'owner_id' => $this->invoice->owner_id,
            'account_id' => $this->invoice->account->getKey(),
            'invoice_id' => $this->invoice->getKey(),
            'contact_id' => $this->invoice->contact_id,
            'ended_at' => $subscriptionEndedAt,
            'stripe_subscription_id' => $stripeSubscriptionId,
        ]);
    }
}
