<?php

namespace App\Http\Resource;

use App\Models\Estimate;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;

/**
 * @mixin Estimate
 */
class EstimateMinimalResource extends BaseResourceWithCustomField
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
        $customFieldValues = $this->getCustomFieldValues();

        return [
            'id' => $this->id,
            'status' => $this->status,
            'isExpired' => $this->checkExpired(),
            'customFields' => $this->getCustomFields($customFieldValues),
            'createdBy' => new UserInitiatorResource($this->createdBy),
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
            'createdAt' => $this->created_at,
            'tag' => TagResource::collection($this->whenLoaded('tag')),
            'itemGroups' => EstimateItemGroupResource::collection($this->whenLoaded('estimateItemGroup')),
            'estimateName' => $this->estimate_name,
            'estimateNumber' => $this->estimate_number,
            'estimateDate' => $this->estimate_date,
            'estimateValidityDuration' => $this->estimate_validity_duration,
            'subTotal' => $this->sub_total,
            'totalTax' => $this->total_tax,
            'totalDiscount' => $this->total_discount,
            'taxPercent' => $this->tax_percent,
            'discountPercent' => $this->discount_percent,
            'grandTotal' => $this->grand_total,
            'totalNoOfEstimates' => $this->opportunity?->estimates_count,
            'currentEstimate' => $this->estimate_number_for_opportunity,
        ];
    }

    private function checkExpired(): bool
    {
        return $this->estimate_validity_duration < now();
    }
}
