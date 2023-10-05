<?php

namespace App\Http\Repositories;

use App\Models\Payment;

class PaymentRepository extends BaseRepository
{
    /**
     * @param Payment $payment
     */
    public function __construct(
        Payment $payment,
    ) {
        $this->model = $payment;
    }

    public function getSummaryPaymentsForInvoice(int $invoiceId)
    {
        return $this->model->newQuery()->where('invoice_id', $invoiceId)->sum('payment_received');
    }
}
