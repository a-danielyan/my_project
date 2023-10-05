<?php

namespace App\Http\Requests\Payment;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'accountId' => [
                    'exists:account,id',
                ],
                'invoiceId' => [
                    'exists:invoice,id',
                ],
                'refundInvoice' => [
                    'exists:invoice,id',
                ],
                'paymentReceived' => [
                    'decimal:0,2',
                ],
                'paymentMethod' => [
                    'string',
                    Rule::in(Payment::AVAILABLE_PAYMENT_METHODS),
                ],
                'paymentSource' => [
                    'string',
                ],
                'paymentProcessor' => [
                    'string',
                    Rule::in(
                        Payment::AVAILABLE_PAYMENT_PROCESSORS,
                    ),
                ],
                'creditCardType' => [
                    'string',
                    Rule::in(Payment::AVAILABLE_CREDIT_CARDS),
                ],
                'paymentDate' => [
                    'date',
                ],
                'note' => [
                    'string',
                ],
                'receivedBy' => [
                    'exists:users,id',
                ],
            ];
    }
}
