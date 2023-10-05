<?php

namespace App\Http\Resource;

use App\Helpers\StorageHelper;
use App\Models\Template;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Template
 */
class TemplateResource extends JsonResource
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
            'entity' => $this->entity,
            'template' => $this->template,
            'name' => $this->name,
            'status' => $this->status,
            'thumbImage' => $this->getImage(),
            'isDefault' => $this->is_default,
            'isShared' => $this->is_shared,
            'tag' => TagResource::collection($this->whenLoaded('tag')),
            'createdBy' => new UserInitiatorResource($this->createdBy),
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
        ];
    }

    private function getImage(): ?string
    {
        return StorageHelper::getSignedStorageUrl($this->thumb_image);
    }
}
