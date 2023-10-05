<?php

namespace App\Http\Resource;

use App\Models\Reminder;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Reminder
 */
class ReminderResource extends JsonResource
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
            'name' => $this->name,
            'relatedEntity' => $this->related_entity,
            'remindEntity' => $this->remind_entity,
            'remindDays' => $this->remind_days,
            'remindType' => $this->remind_type,
            'condition' => $this->condition,
            'sender' => $this->sender,
            'reminderCC' => $this->reminder_cc,
            'reminderBCC' => $this->reminder_bcc,
            'subject' => $this->subject,
            'reminderText' => $this->reminder_text,
            'status' => $this->status,
            'createdBy' => new UserInitiatorResource($this->createdBy),
            'updatedBy' => new UserInitiatorResource($this->updatedBy),
        ];
    }
}
