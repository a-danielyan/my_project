<?php

namespace App\Http\RequestTransformers\Payment;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class PaymentStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'paymentReceived' => 'payment_received',
                'paymentMethod' => 'payment_method',
                'paymentSource' => 'payment_source',
                'paymentProcessor' => 'payment_processor',
                'creditCardType' => 'credit_card_type',
                'paymentDate' => 'payment_date',
                'note' => 'notes',
                'accountId' => 'account_id',
                'invoiceId' => 'invoice_id',
                'receivedBy' => 'received_by',
                'refundInvoice' => 'refund_invoice',
            ];
    }
}
