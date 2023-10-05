<?php

namespace App\Http\Resource;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $this->whenLoaded('role');

        return
            [
                'id' => $this->id,
                'firstName' => $this->first_name,
                'lastName' => $this->last_name,
                'status' => $this->status,
                'email' => $this->email,
                'avatar' => $this->avatar,
                'role' => new RoleWithPermissionsResource($this->role),
                'createdBy' => new UserInitiatorResource($this->createdBy),
                'updatedBy' => new UserInitiatorResource($this->updatedBy),
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
                'userSignature' => $this->user_signature,
                'dashboardBlocks' => $this->dashboard_blocks,
            ];
    }
}
