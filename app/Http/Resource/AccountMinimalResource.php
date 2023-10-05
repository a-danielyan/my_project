<?php

namespace App\Http\Resource;

use App\Helpers\StorageHelper;
use App\Models\Account;
use App\Traits\ColumnsFilterOnResourceTrait;
use App\Traits\GetRecordStatusTrait;
use Illuminate\Http\Request;

/**
 * @mixin Account
 */
class AccountMinimalResource extends BaseResourceWithCustomField
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
            'customFields' => $this->getCustomFields($customFieldValues),
            'createdBy' => new UserInitiatorResource($this->createdBy),
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'tag' => TagResource::collection($this->whenLoaded('tag')),
            'status' => $this->getStatus(),
            'accountPayable' => ContactResource::collection($this->accountsPayable),
            'lead' => new LeadResource($this->whenLoaded('lead')),
            'deviceCount' => 0, //@todo add relation later
            'countSales' => 0, //@todo add relation later
            'totalSalesValue' => 0,//@todo add relation later
            'avatar' => $this->getAvatar(),
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
