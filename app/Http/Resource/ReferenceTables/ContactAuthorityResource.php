<?php

namespace App\Http\Resource\ReferenceTables;

use App\Models\ContactAuthority;
use App\Traits\GetRecordStatusTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ContactAuthority
 */
class ContactAuthorityResource extends JsonResource
{
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
            'status' => $this->getStatus(),
        ];
    }
}
