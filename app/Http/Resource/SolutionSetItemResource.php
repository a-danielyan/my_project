<?php

namespace App\Http\Resource;

use App\Models\SolutionSetItems;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SolutionSetItems
 */
class SolutionSetItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'product' => new ProductResource($this->product->load('customFields')),
            'quantity' => $this->quantity,
            'price' => $this->price,
            'description' => $this->description,
            'discount' => $this->discount,
        ];
    }
}
