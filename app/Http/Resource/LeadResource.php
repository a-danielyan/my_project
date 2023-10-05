<?php

namespace App\Http\Resource;

use App\Helpers\StorageHelper;
use App\Models\Lead;
use App\Traits\ColumnsFilterOnResourceTrait;
use App\Traits\GetRecordStatusTrait;
use Illuminate\Http\Request;

/**
 * @mixin Lead
 */
class LeadResource extends BaseResourceWithCustomField
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
        if ($this->resource == null) {
            return [];
        }

        $customFieldValues = $this->getCustomFieldValues();

        return [
            'id' => $this->id,
            'salutation' => $this->salutation,
            'customFields' => $this->getCustomFields($customFieldValues),
            'createdBy' => new UserInitiatorResource($this->createdBy),
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'tag' => TagResource::collection($this->whenLoaded('tag')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'status' => $this->getStatus(),
            'avatar' => $this->getAvatar(),
            'activity' => ActivityMinimalResource::collection($this->whenLoaded('activity')),
            'internalNote' => new  ActivityMinimalResource($this->whenLoaded('internalNote')),
        ];
    }

    private function getAvatar(): string
    {
        if (empty($this->avatar)) {
            return '';
        }
        if (str_starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }

        return StorageHelper::getSignedStorageUrl($this->avatar);
    }
}
