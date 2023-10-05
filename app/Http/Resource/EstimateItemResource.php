<?php

namespace App\Http\Resource;

use App\Models\EstimateItem;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin EstimateItem
 */
class EstimateItemResource extends JsonResource
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
            'quantity' => $this->quantity,
            'description' => $this->description,
            'discount' => $this->discount,
            'product' => new ProductResource($this->product),
            'parentId' => $this->parent_id,
            'combinePrice' => $this->combine_price,
            'taxPercent' => $this->tax_percent,
            'id' => $this->id,
        ];
    }
}
