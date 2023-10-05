<?php

namespace App\Http\Resource;

use App\Models\TermsAndConditions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TermsAndConditions
 */
class TermsAndConditionsResource extends JsonResource
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
            'entity' => $this->entity,
            'termsAndCondition' => $this->terms_and_condition,
        ];
    }
}
