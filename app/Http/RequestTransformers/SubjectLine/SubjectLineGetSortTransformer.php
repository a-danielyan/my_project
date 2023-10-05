<?php

namespace App\Http\RequestTransformers\SubjectLine;

use App\Http\RequestTransformers\BaseGetSortTransformer;

class SubjectLineGetSortTransformer extends BaseGetSortTransformer
{
    protected function getMap(): array
    {
        return array_merge(parent::getMap(), [
            'activityType' => 'activity_type',
            'activityStatus' => 'activity_status',
            'dueDate' => 'due_date',
            'relatedTo' => 'related_to',
            'relatedToId' => 'related_to_id',
            'startedAt' => 'started_at',
            'endedAt' => 'ended_at',
            'beforeDate' => 'beforeDate',
            'afterDate' => 'afterDate',
        ]);
    }
}
