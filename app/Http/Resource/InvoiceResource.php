<?php

namespace App\Http\Resource;

use App\Models\Invoice;
use Illuminate\Http\Request;

/**
 * @mixin Invoice
 */
class InvoiceResource extends InvoiceMinimalResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            parent::toArray($request),
            [
                'opportunity' => new OpportunityResource($this->opportunity),
                'account' => new AccountResource($this->account, customFieldList: [
                    'first-name',
                    'last-name',
                    'account-name',
                    'sales-tax',
                ]),
                'clientAdmin' => new ContactResource($this->client),
                'totalNoOfEstimates' => $this->estimate?->opportunity?->estimates_count,
                'currentEstimate' => $this->estimate?->estimate_number_for_opportunity,
                'payments' => PaymentResource::collection($this->whenLoaded('invoicePayments')),
            ],
        );
    }
}
