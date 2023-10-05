<?php

namespace App\Http\Resource;

use App\Models\PaymentProfile;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PaymentProfile
 */
class PaymentProfileResource extends JsonResource
{
    /**
     * Trait for column-wise filtering in resource
     */
    use ColumnsFilterOnResourceTrait;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'account' => new AccountResource($this->account, customFieldList: [
                'first-name',
                'last-name',
                'account-name',
            ]),
            'paymentName' => $this->payment_name,
            'paymentMethod' => $this->payment_method,
            'billingStreetAddress' => $this->billing_street_address,
            'billingCity' => $this->billing_city,
            'billingState' => $this->billing_state,
            'billingPostalCode' => $this->billing_postal_code,
            'billingCountry' => $this->billing_country,
            'createdBy' => new UserInitiatorResource($this->createdBy),
        ];
    }
}
