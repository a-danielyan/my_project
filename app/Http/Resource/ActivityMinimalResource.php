<?php

namespace App\Http\Resource;

use App\Models\Activity;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Activity
 */
class ActivityMinimalResource extends JsonResource
{
    /**
     * Trait for column-wise filtering in resource
     */
    use ColumnsFilterOnResourceTrait;


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
            'relatedTo' => new UserInitiatorResource($this->relatedUser),
            'startedAt' => $this->started_at,
            'endedAt' => $this->ended_at,
            'activityType' => $this->activity_type,
            'activityStatus' => $this->activity_status,
            'priority' => $this->priority,
            'dueDate' => $this->due_date,
            'subject' => $this->subject,
            'description' => $this->description,
            'createdBy' => new UserInitiatorResource($this->createdBy),
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
            'reminders' => ActivityReminderResource::collection($this->reminders),
            'tag' => TagResource::collection($this->whenLoaded('tag')),
        ];
    }
}
