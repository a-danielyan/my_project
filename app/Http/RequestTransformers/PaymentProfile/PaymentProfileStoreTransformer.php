<?php

namespace App\Http\RequestTransformers\PaymentProfile;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class PaymentProfileStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'accountId' => 'account_id',
                'paymentName' => 'payment_name',
                'paymentMethod' => 'payment_method',
                'billingStreetAddress' => 'billing_street_address',
                'billingCity' => 'billing_city',
                'billingState' => 'billing_state',
                'billingPostalCode' => 'billing_postal_code',
                'billingCountry' => 'billing_country',
            ];
    }
}
