<?php

namespace App\Http\Resource;

use App\Models\Sequence\SequenceEntityAssociation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SequenceEntityAssociation
 */
class EntitySequenceResource extends JsonResource
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

            'entityType' => str_replace('App\Models\\', '', $this->entity_type),
            'entityId' => $this->entity_id,
            'relatedEntity' => $this->getRelatedEntity(),
            'countEmailsSent' => $this->count_emails_sent,
        ];
    }

    private function getRelatedEntity(): ContactMinimalResource|LeadResource
    {
        if ($this->entity_type === 'App\Models\Lead') {
            return new LeadResource($this->entity);
        } else {
            return new ContactMinimalResource($this->entity);
        }
    }
}
