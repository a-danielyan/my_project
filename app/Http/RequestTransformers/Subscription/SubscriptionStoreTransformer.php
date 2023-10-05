<?php

namespace App\Http\RequestTransformers\Subscription;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class SubscriptionStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'tag' => 'tag',
                'relatedTo' => 'related_to',
                'startedAt' => 'started_at',
                'endedAt' => 'ended_at',
                'activityType' => 'activity_type',
                'activityStatus' => 'activity_status',
                'priority' => 'priority',
                'dueDate' => 'due_date',
                'subject' => 'subject',
                'relatedToEntity' => 'related_to_entity',
                'relatedToId' => 'related_to_id',
                'description' => 'description',
                'reminderAt' => 'reminder_at',
                'reminderType' => 'reminder_type',
            ];
    }
}
