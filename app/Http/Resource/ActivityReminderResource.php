<?php

namespace App\Http\Resource;

use App\Models\ActivityReminder;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ActivityReminder
 */
class ActivityReminderResource extends JsonResource
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
            'reminderType' => $this->reminder_type,
            'reminderTime' => $this->reminder_time,
            'reminderUnit' => $this->reminder_unit,
        ];
    }
}
