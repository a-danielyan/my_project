<?php

namespace App\Http\Resource;

use App\Models\Product;
use App\Traits\ColumnsFilterOnResourceTrait;
use App\Traits\GetRecordStatusTrait;
use Illuminate\Http\Request;

/**
 * @mixin Product
 */
class ProductResource extends BaseResourceWithCustomField
{
    /**
     * Trait for column-wise filtering in resource
     */
    use ColumnsFilterOnResourceTrait;
    use GetRecordStatusTrait;

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
            'customFields' => $this->getCustomFields($customFieldValues),
            'createdBy' => new UserInitiatorResource($this->createdBy),
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
            'createdAt' => $this->created_at,
            'tag' => TagResource::collection($this->whenLoaded('tag')),
            'status' => $this->getStatus(),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
        ];
    }
}
