<?php

namespace App\Console\Commands;

use App\Http\Repositories\InvoiceRepository;
use Illuminate\Console\Command;

class UpdateInvoiceStatus extends Command
{
    protected $signature = 'app:update-invoice-status';

    protected $description = 'Handle automatically update invoice statuses';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        /** @var InvoiceRepository $invoiceRepository */
        $invoiceRepository = resolve(InvoiceRepository::class);

        //Automatically set invoice status to Payment Pending
        $invoiceRepository->updateSentInvoicesToPaymentPendingStatus();
    }
}
