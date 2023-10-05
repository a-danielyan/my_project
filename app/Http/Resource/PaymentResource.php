<?php

namespace App\Http\Resource;

use App\Models\Payment;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Payment
 */
class PaymentResource extends JsonResource
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
            'invoice' => $this->whenLoaded('invoice'),
            'paymentReceived' => $this->payment_received,
            'paymentMethod' => $this->payment_method,
            'paymentSource' => $this->payment_source,
            'paymentProcessor' => $this->payment_processor,
            'creditCardType' => $this->credit_card_type,
            'paymentDate' => $this->payment_date,
            'notes' => $this->notes,
            'receivedBy' => $this->receivedBy,
            'refundInvoice' => $this->refundInvoice,
        ];
    }
}
