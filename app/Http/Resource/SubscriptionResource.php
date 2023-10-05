<?php

namespace App\Http\Resource;

use App\Models\Subscription;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Subscription
 */
class SubscriptionResource extends JsonResource
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
            'subscriptionName' => $this->subscription_name,
            'owner' => $this->owner,
            'account' => $this->account,
            'invoice' => $this->invoice,
            'contact' => $this->contact,
            'parentPO' => $this->parent_po,
            'previousPO' => $this->previous_po,
            'orderZnumber' => $this->order_z_number,
            'endedAt' => $this->ended_at,
            'createdBy' => new UserInitiatorResource($this->createdBy),
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
            'attachments' => $this->attachments,
            'devices' => $this->devices,
            'items' => $this->items,


        ];
    }
}
