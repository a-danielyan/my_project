<?php

namespace App\Http\Requests\Subscription;

use App\Models\Activity;
use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'relatedTo' => [
                    'required',
                    'exists:users,id',
                ],
                'startedAt' => [
                    'required',
                    'date',
                ],
                'endedAt' => [
                    'date',
                ],
                'activityType' => [
                    'required',
                    Rule::in(Activity::ACTIVITY_TYPES),
                ],
                'activityStatus' => [
                    'required',
                    Rule::in(Activity::ACTIVITY_STATUSES),
                ],
                'priority' => [
                    'required',
                    Rule::in(Activity::PRIORITY_STATUSES),
                ],

                'dueDate' => [
                    'date',
                ],
                'subject' => [
                    'required',
                    'string',
                ],
                'relatedToEntity' => [
                    'required',
                    'string',
                ],
                'relatedToId' => [
                    'required',
                    'int',
                ],
                'description' => [
                    'string',
                ],
                'reminderAt' => [
                    'date',
                ],
                'reminderType' => [
                    'string',
                ],
                'tag' => [
                    'array',
                ],
                'tag.*.id' => [
                    'int',
                    Rule::exists('tag', 'id')->where('entity_type', Subscription::class),
                ],

            ];
    }
}
