<?php

namespace App\Http\Requests\PaymentProfile;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'accountId' => [
                    'exists:account,id',
                ],

                'paymentName' => [
                    'string',
                ],
                'paymentMethod' => [
                    'string',
                    Rule::in([
                        Payment::PAYMENT_METHOD_CREDIT_CARD,
                        Payment::PAYMENT_METHOD_ACH,
                        Payment::PAYMENT_METHOD_CHECK,
                        Payment::PAYMENT_METHOD_CASH,
                    ]),
                ],

                'billingStreetAddress' => [
                    'string',
                ],
                'billingCity' => [
                    'string',
                ],
                'billingState' => [
                    'string',
                ],
                'billingPostalCode' => [
                    'string',
                ],
                'billingCountry' => [
                    'string',
                ],
            ];
    }
}
