<?php

namespace App\Http\Requests\Activity;

use App\Models\Activity;
use App\Models\ActivityReminder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityStoreRequest extends FormRequest
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
                    'required',
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
                'reminders' => [
                    'array',
                    'nullable',
                ],
                'reminders.*.reminderType' => [
                    'string',
                    'required',
                    Rule::in(ActivityReminder::AVAILABLE_REMINDER_TYPE),
                ],
                'reminders.*.reminderUnit' => [
                    'string',
                    'required',
                    Rule::in(ActivityReminder::AVAILABLE_REMINDER_UNITS),
                ],
                'reminders.*.reminderTime' => [
                    'integer',
                    'required',
                ],
                'tag' => [
                    'array',
                ],
                'tag.*.id' => [
                    'int',
                    Rule::exists('tag', 'id')->where('entity_type', Activity::class),
                ],

            ];
    }
}
