<?php

namespace App\Http\Resource;

use App\Models\User;
use App\Traits\GetRecordStatusTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class BaseUserResource
 * @package App\Http\Resources\Common\User
 * @mixin User
 */
abstract class BaseUserResource extends JsonResource
{
    use GetRecordStatusTrait;

    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'status' => $this->getStatus(),
            'email' => $this->email,
            'avatar' => $this->avatar,
            'role' => new RoleWithPermissionsResource($this->role),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
