<?php

namespace App\Http\Resource;

use App\Models\Role;
use App\Traits\ColumnsFilterOnResourceTrait;
use App\Traits\GetRecordStatusTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @mixin Role
 */
class RoleResource extends JsonResource
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'createdBy' => new UserInitiatorResource($this->createdBy),
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
            'status' => $this->getStatus(),
        ];
    }
}
