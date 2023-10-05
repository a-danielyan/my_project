<?php

namespace App\Jobs;

use App\Http\Repositories\EstimateRepository;
use App\Http\Services\StripeService;
use App\Models\Estimate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

class FinalizeStripeQuote implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
        Log::debug('we start finalize quote ', [
            'estimateId' => $this->estimate->getKey(),
        ]);
        if (empty($this->quoteId)) {
            Log::error('We dont have saved quote', [
                'estimateId' => $this->estimate->getKey(),
            ]);

            return;
        }

        /** @var StripeService $stripeService */
        $stripeService = resolve(StripeService::class);
        try {
            $stripeService->finalizeQuote(
                $this->quoteId,
            );
        } catch (ApiErrorException $e) {
            Log::error('Cant finalize quote ', [
                'quoteId' => $this->quoteId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
