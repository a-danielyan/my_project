<?php

namespace App\Http\Resource;

use App\Models\Preference;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Preference
 */
class PreferenceResource extends JsonResource
{
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
            'entity' => $this->entity,
            'settings' => $this->settings,
        ];
    }
}
