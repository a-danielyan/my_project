<?php

namespace App\Http\Resource;

use App\Models\CustomField;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CustomField
 */
class CustomFieldResource extends JsonResource
{
    /**
     * Trait for column-wise filtering in resource
     */
    use ColumnsFilterOnResourceTrait;


    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'entityType' => $this->entity_type,
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'lookupType' => $this->lookup_type,
            'sortOrder' => $this->sort_order,
            'isRequired' => $this->is_required,
            'isUnique' => $this->is_unique,
            'isMultiple' => $this->is_multiple,
            'isReadOnly' => $this->is_readonly,
            'parentId' => $this->parent_id,
            'options' => CustomFieldOptionResource::collection($this->options),
            'width' => $this->width,
            'tooltip' => $this->tooltip,
            'tooltipType' => $this->tooltip_type,
            'property' => $this->property,
        ];
    }
}
