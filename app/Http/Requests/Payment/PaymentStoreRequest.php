<?php

namespace App\Http\Requests\Payment;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'accountId' => [
                    'required',
                    'exists:account,id',
                ],
                'invoiceId' => [
                    'required',
                    'exists:invoice,id',
                ],
                'refundInvoice' => [
                    'exists:invoice,id',
                ],
                'paymentReceived' => [
                    'required',
                    'decimal:0,2',
                ],
                'paymentMethod' => [
                    'required',
                    'string',
                    Rule::in(Payment::AVAILABLE_PAYMENT_METHODS),
                ],
                'paymentSource' => [
                    'string',
                ],
                'paymentProcessor' => [
                    'string',
                    Rule::in(Payment::AVAILABLE_PAYMENT_PROCESSORS),
                ],
                'creditCardType' => [
                    'string',
                    'required_if:paymentMethod,' . Payment::PAYMENT_METHOD_CREDIT_CARD,
                    Rule::in(Payment::AVAILABLE_CREDIT_CARDS),
                ],
                'paymentDate' => [
                    'required',
                    'date',
                ],
                'note' => [
                    'string',
                ],
                'receivedBy' => [
                    'required',
                    'exists:users,id',
                ],
            ];
    }
}
