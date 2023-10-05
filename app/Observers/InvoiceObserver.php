<?php

namespace App\Observers;

use App\Models\Invoice;

class InvoiceObserver
{
    public $afterCommit = true;

    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        if (
            $invoice->status == Invoice::INVOICE_STATUS_PAYMENT_COMPLETED ||
            $invoice->status == Invoice::INVOICE_STATUS_TERMS_ACCEPTED ||
            $invoice->status == Invoice::INVOICE_STATUS_PARTIALLY_PAID
        ) {
            $invoice->order_date = now();
            $invoice->save();
        }
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "restored" event.
     */
    public function restored(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "force deleted" event.
     */
    public function forceDeleted(Invoice $invoice): void
    {
        //
    }
}
