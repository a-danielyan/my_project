<?php

namespace App\Http\Resource;

use App\Models\Sequence\Sequence;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Sequence
 */
class SequenceResource extends JsonResource
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
            'name' => $this->name,
            'startDate' => $this->start_date,
            'isActive' => $this->is_active,
            'templates' => TemplateSequenceResource::collection($this->whenLoaded('templatesAssociation')),
            'entity' => EntitySequenceResource::collection($this->whenLoaded('entityRelation')),
            'createdBy' => new UserInitiatorResource($this->createdBy),
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
        ];
    }
}
