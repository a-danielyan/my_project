<?php

namespace App\Http\Resource;

use App\Models\Permission;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Permission
 */
class PermissionResource extends JsonResource
{
    public function toArray($request): array
    {
        $role = $request->role;

        return [
            'id' => $this->id,
            'name' => $this->customField?->name,
            'group' => str_replace('App\\Models\\', '', $this->customField?->entity_type),
            'action' => $this->action,
            'attached' => $this->roles->contains($role->getKeyName(), $role->getKey()),
        ];
    }
}
