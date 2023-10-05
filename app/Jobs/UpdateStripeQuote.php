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

class UpdateStripeQuote implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use GenerateStripeLineItemsArrayTrait;

    private EstimateRepository $estimateRepository;
    private ?string $quoteId;

    /**
     * Create a new job instance.
     */
    public function __construct(private Estimate $estimate)
    {
        $this->estimateRepository = resolve(EstimateRepository::class);
        $this->quoteId = $estimate->stripe_quote_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug('we start update quote ', ['estimateId' => $this->estimate->getKey()]);
        if (empty($this->quoteId)) {
            Log::error('We dont have saved quote for estimate', ['estimateId' => $this->estimate->getKey()]);

            return;
        }

        /** @var StripeService $stripeService */
        $stripeService = resolve(StripeService::class);
        try {
            $stripeService->updateQuote(
                $this->quoteId,
                $this->generateLineItemsArray(),
                $this->estimate->estimate_validity_duration->timestamp,
            );
        } catch (CustomErrorException $e) {
            Log::error('Cant update quote for estimate ', [
                'quoteId' => $this->quoteId,
                'timestamp' => $this->estimate->estimate_validity_duration->timestamp,
                'error' => $e->getMessage(),
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Cant update quote for estimate ', [
                'quoteId' => $this->quoteId,
                'lineItems' => $this->generateLineItemsArray(),
                'timestamp' => $this->estimate->estimate_validity_duration->timestamp,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
