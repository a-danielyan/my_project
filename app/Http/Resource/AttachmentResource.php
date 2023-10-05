<?php

namespace App\Http\Resource;

use App\Helpers\StorageHelper;
use App\Models\AccountAttachment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AccountAttachment
 */
class AttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'attachmentLink' => $this->getAttachmentLink(),
            'updatedBy' => new UserInitiatorResource($this->createdBy),
            'createdAt' => $this->created_at,
        ];
    }

    private function getAttachmentLink(): ?string
    {
        if (!empty($this->attachment_link)) {
            return $this->attachment_link;
        }

        return StorageHelper::getSignedStorageUrl($this->attachment_file);
    }
}
