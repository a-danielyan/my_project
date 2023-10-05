<?php

namespace App\Http\Resource;

use App\Models\License;
use App\Traits\ColumnsFilterOnResourceTrait;
use App\Traits\GetRecordStatusTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin License
 */
class LicenseResource extends JsonResource
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
        $this->whenLoaded('tag');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'licenseType' => $this->license_type,
            'licenseDurationInMonth' => $this->license_duration_in_month,
            'createdBy' => new UserInitiatorResource($this->createdBy),
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
            'createdAt' => $this->created_at,
            'tag' => TagResource::collection($this->tag),
            'status' => $this->getStatus(),
        ];
    }
}
