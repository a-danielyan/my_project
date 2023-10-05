<?php

namespace App\Http\Resource;

use App\Models\CustomFieldOption;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CustomFieldOption
 */
class CustomFieldOptionResource extends JsonResource
{
    /**
     * Trait for column-wise filtering in resource
     */
    use ColumnsFilterOnResourceTrait;


    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sortOrder' => $this->sort_order,
        ];
    }
}
