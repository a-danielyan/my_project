<?php

namespace Tests\Command;

use App\Console\Commands\UpdateInvoiceStatus;
use App\Models\Invoice;
use Tests\TestCase;

class ConvertInvoiceStatusTest extends TestCase
{
    /**
     * @return void
     */
    public function test_convert_invoice_status_to_pending_payment(): void
    {
        $invoice = Invoice::factory()->createOne(
            ['status' => Invoice::INVOICE_STATUS_SENT, 'sent_at' => now()->subDays(10)],
        );

        $this->artisan(UpdateInvoiceStatus::class)->assertSuccessful();

        $this->assertDatabaseHas('invoice', [
            'id' => $invoice->getKey(),
            'status' => Invoice::INVOICE_STATUS_PAYMENT_PENDING,
        ]);
    }
}
