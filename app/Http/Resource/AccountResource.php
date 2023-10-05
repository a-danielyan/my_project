<?php

namespace App\Http\Resource;

use App\Models\Account;
use App\Traits\ColumnsFilterOnResourceTrait;
use App\Traits\GetRecordStatusTrait;
use Illuminate\Http\Request;

/**
 * @mixin Account
 */
class AccountResource extends AccountMinimalResource
{
    /**
     * Trait for column-wise filtering in resource
     */
    use ColumnsFilterOnResourceTrait;
    use GetRecordStatusTrait;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        if ($this->resource == null) {
            return [];
        }

        return array_merge(parent::toArray($request), [
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'contacts' => ContactMinimalResource::collection($this->whenLoaded('contacts')),
            'invoices' => InvoiceMinimalResource::collection($this->whenLoaded('invoices')),
            'opportunities' => OpportunityMinimalResource::collection($this->whenLoaded('opportunities')),
            'internalNote' => new  ActivityMinimalResource($this->whenLoaded('internalNote')),
        ]);
    }
}
