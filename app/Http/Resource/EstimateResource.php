<?php

namespace App\Http\Resource;

use App\Models\Estimate;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;

/**
 * @mixin Estimate
 */
class EstimateResource extends EstimateMinimalResource
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
        return array_merge(parent::toArray($request), [
            'opportunity' => new OpportunityMinimalResource($this->whenLoaded('opportunity')),
            'contact' => new ContactMinimalResource($this->whenLoaded('contact'), customFieldList: [
                'email',
                'first-name',
                'last-name',
            ]),
            'account' => new  AccountResource($this->whenLoaded('account'), customFieldList: [
                'account-name',
                'sales-tax',
            ]),
            'invoice' => new InvoiceMinimalResource($this->whenLoaded('invoice')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
        ]);
    }
}
