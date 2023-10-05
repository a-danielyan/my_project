<?php

namespace App\Http\Resource;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Role
 */
class RoleWithPermissionsResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $this->whenLoaded('permissions');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'permission' => RolePermissionShortResource::collection($this->permissions),
        ];
    }
}
