<?php

namespace App\Http\Requests\PaymentProfile;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentProfileStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'accountId' => [
                    'required',
                    'exists:account,id',
                ],
                'paymentName' => [
                    'required',
                    'string',
                ],
                'paymentMethod' => [
                    'required',
                    'string',
                    Rule::in([
                        Payment::PAYMENT_METHOD_CREDIT_CARD,
                        Payment::PAYMENT_METHOD_ACH,
                        Payment::PAYMENT_METHOD_CHECK,
                        Payment::PAYMENT_METHOD_CASH,
                    ]),
                ],
                'billingStreetAddress' => [
                    'required',
                    'string',
                ],
                'billingCity' => [
                    'required',
                    'string',
                ],
                'billingState' => [
                    'string',
                ],
                'billingPostalCode' => [
                    'required',
                    'string',
                ],
                'billingCountry' => [
                    'required',
                    'string',
                ],
            ];
    }
}
