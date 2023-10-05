<?php

namespace App\Jobs;

use App\Exceptions\CustomErrorException;
use App\Http\Repositories\EstimateRepository;
use App\Http\Services\StripeService;
use App\Models\Estimate;
use App\Traits\GenerateStripeLineItemsArrayTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

class CreateStripeQuote implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use GenerateStripeLineItemsArrayTrait;

    private EstimateRepository $estimateRepository;

    /**
     * Create a new job instance.
     */
    public function __construct(private Estimate $estimate)
    {
        $this->estimateRepository = resolve(EstimateRepository::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug('we start creating quote', ['estimateId' => $this->estimate->getKey()]);
        $customerId = $this->estimate->account->stripe_customer_id;
        if (empty($customerId)) {
            Log::error('We cant create quote. CustomerId missed', [
                'estimateId' => $this->estimate->getKey(),
                'accountId' => $this->estimate->account->getKey(),
            ]);

            return;
        }

        /** @var StripeService $stripeService */
        $stripeService = resolve(StripeService::class);
        try {
            $stripeQuote = $stripeService->createQuote(
                $customerId,
                $this->generateLineItemsArray(),
                $this->estimate->estimate_validity_duration?->timestamp,
            );

            $this->estimateRepository->update($this->estimate, ['stripe_quote_id' => $stripeQuote->id]);
        } catch (CustomErrorException $e) {
            Log::error('Cant create quote for estimate ', [
                'estimateId' => $this->estimate->getKey(),
                'customerId' => $customerId,
                'timestamp' => $this->estimate->estimate_validity_duration?->timestamp,
                'error' => $e->getMessage(),
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Cant create quote for estimate ', [
                'estimateId' => $this->estimate->getKey(),
                'customerId' => $customerId,
                'lineItems' => $this->generateLineItemsArray(),
                'timestamp' => $this->estimate->estimate_validity_duration?->timestamp,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
