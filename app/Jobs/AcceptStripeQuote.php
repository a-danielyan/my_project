<?php

namespace App\Jobs;

use App\Http\Repositories\InvoiceRepository;
use App\Http\Services\StripeService;
use App\Models\Estimate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

class AcceptStripeQuote implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private InvoiceRepository $invoiceRepository;
    private ?string $quoteId;

    /**
     * Create a new job instance.
     */
    public function __construct(private Estimate $estimate)
    {
        $this->invoiceRepository = resolve(InvoiceRepository::class);
        $this->quoteId = $estimate->stripe_quote_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug('we start accepting quote ', ['estimateId' => $this->estimate->getKey()]);
        if (empty($this->quoteId)) {
            Log::error('We dont have saved quote for estimate', ['estimateId' => $this->estimate->getKey()]);

            return;
        }

        /** @var StripeService $stripeService */
        $stripeService = resolve(StripeService::class);
        try {
            $stripeQuote = $stripeService->acceptQuote(
                $this->quoteId,
            );
        } catch (ApiErrorException $e) {
            Log::error('Cant accept quote ', [
                'estimateId' => $this->estimate->getKey(),
                'quoteId' => $this->quoteId,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        try {
            Log::debug('we start finalize Invoice', [
                'estimateId' => $this->estimate->getKey(),
                'invoiceId' => $stripeQuote->invoice,
            ]);
            $stripeService->finalizeInvoice(
                $stripeQuote->invoice,
            );
            $this->invoiceRepository->updateByParams(
                ['estimate_id' => $this->estimate->getKey()],
                ['stripe_invoice_id' => $stripeQuote->invoice],
            );
        } catch (ApiErrorException $e) {
            Log::error('Cant finalize invoice', [
                'estimateId' => $this->estimate->getKey(),
                'invoiceId' => $stripeQuote->invoice,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
