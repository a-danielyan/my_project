<?php

namespace App\Http\RequestTransformers\Reminder;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class ReminderStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'name' => 'name',
                'relatedEntity' => 'related_entity',
                'remindEntity' => 'remind_entity',
                'remindDays' => 'remind_days',
                'remindType' => 'remind_type',
                'condition' => 'condition',
                'sender' => 'sender',
                'reminderCC' => 'reminder_cc',
                'reminderBCC' => 'reminder_bcc',
                'subject' => 'subject',
                'reminderText' => 'reminder_text',
                'status' => 'status',
            ];
    }
}
