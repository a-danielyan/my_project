<?php

namespace App\Jobs;

use App\Helpers\CommonHelper;
use App\Http\Repositories\InvoiceRepository;
use App\Http\Repositories\PaymentRepository;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateInvoiceBalanceDue implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $invoiceId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /** @var InvoiceRepository $invoiceRepository */
        $invoiceRepository = resolve(InvoiceRepository::class);
        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = resolve(PaymentRepository::class);
        /** @var Invoice $invoice */
        $invoice = $invoiceRepository->findById($this->invoiceId);
        if (empty($invoice)) {
            Log::error('We cant find invoice with id =' . $this->invoiceId);

            return;
        }
        $receivedPayments = $paymentRepository->getSummaryPaymentsForInvoice($this->invoiceId);

        $invoiceBalance_due = $invoice->grand_total - $receivedPayments;
        $cronUser = CommonHelper::getCronUser();
        $invoiceRepository->update(
            $invoice,
            ['updated_by' => $cronUser->getKey(), 'balance_due' => $invoiceBalance_due],
        );
    }
}
