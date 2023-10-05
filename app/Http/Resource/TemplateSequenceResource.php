<?php

namespace App\Http\Resource;

use App\Models\Sequence\SequenceTemplateAssociation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SequenceTemplateAssociation
 */
class TemplateSequenceResource extends JsonResource
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
            'template' => new TemplateResource($this->template),
            'sendAfter' => $this->send_after,
            'sendAfterUnit' => $this->send_after_unit,
        ];
    }
}
