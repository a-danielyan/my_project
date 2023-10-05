<?php

namespace App\Http\Resource;

use App\Models\EstimateShippingGroupItems;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin EstimateShippingGroupItems
 */
class EstimateItemGroupResource extends JsonResource
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
            'contact' => new ContactMinimalResource($this->whenLoaded('contact')),
            'address' => $this->address,
            'items' => EstimateItemResource::collection($this->whenLoaded('items')),
            'id' => $this->id,
        ];
    }
}
