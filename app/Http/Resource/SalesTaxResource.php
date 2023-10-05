<?php

namespace App\Http\Resource;

use App\Models\SalesTax;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SalesTax
 */
class SalesTaxResource extends JsonResource
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
            'stateCode' => $this->state_code,
            'tax' => $this->tax,
            'createdBy' => new UserInitiatorResource($this->createdBy),
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
