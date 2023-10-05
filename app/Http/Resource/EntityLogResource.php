<?php

namespace App\Http\Resource;

use App\Models\EntityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin EntityLog
 */
class EntityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'previousValue' => $this->previous_value,
            'field' => $this->field?->name ?? '',
            'newValue' => $this->new_value,
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
            'createdAt' => $this->created_at,
            'updateId' => $this->update_id,
            'logType' => $this->log_type,
            'activity' => new ActivityMinimalResource($this->activity),
        ];
    }
}
