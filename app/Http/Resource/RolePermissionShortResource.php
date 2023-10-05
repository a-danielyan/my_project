<?php

namespace App\Http\Resource;

use App\Models\Permission;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Permission
 */
class RolePermissionShortResource extends JsonResource
{
    public function toArray($request): array
    {
        $this->whenLoaded('customField');
        return [
            'id' => $this->id,
            'name' => $this->customField?->name,
            'group' => str_replace('App\\Models\\', '', $this->customField?->entity_type),
            'action' => $this->action,
        ];
    }
}
