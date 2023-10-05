<?php

namespace App\Http\Requests\Reminder;

use App\Models\Reminder;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReminderUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'name' => [
                    'string',
                ],
                'relatedEntity' => [
                    'string',
                    Rule::in([Reminder::RELATED_ENTITY_SUBSCRIPTION, Reminder::RELATED_ENTITY_INVOICE]),
                ],
                'remindEntity' => [
                    'string',
                    Rule::in(
                        Reminder::REMIND_ENTITY_ACCOUNT,
                        Reminder::REMIND_ENTITY_CONTACT,
                        Reminder::REMIND_ENTITY_ME,
                    ),
                ],
                'remindDays' => [
                    'integer',
                ],
                'remindType' => [
                    'string',
                    Rule::in([Reminder::REMIND_TYPE_BEFORE, Reminder::REMIND_TYPE_AFTER]),
                ],
                'condition' => [
                    'string',
                ],
                'sender' => [
                    'array',
                ],
                'sender.*' => [
                    'email',
                ],
                'reminderCC' => [
                    'array',
                ],
                'reminderCC.*' => [
                    'email',
                ],
                'reminderBCC' => [
                    'array',
                ],
                'reminderBCC.*' => [
                    'email',
                ],
                'subject' => [
                    'string',
                ],
                'reminderText' => [
                    'string',
                ],
                'status' => [
                    'string',
                    Rule::in([User::STATUS_INACTIVE, User::STATUS_ACTIVE]),
                ],
            ];
    }
}
