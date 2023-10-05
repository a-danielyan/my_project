<?php

namespace App\Http\Requests\Account;

use App\Models\Activity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountTrainingUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'trainingDate' => [
                    'date',
                ],
                'dueDate' => [
                    'required_with:subject',
                    'date',
                ],
                'trainedBy' => [
                    'exists:users,id',
                ],
                'note' => [
                    'string',
                ],
                'subject' => [
                    'string',
                ],
                'description' => [
                    'required_with:subject',
                    'string',
                ],
                'priority' => [
                    'string',
                    Rule::in(Activity::PRIORITY_STATUSES),
                ],
                'relatedTo' => [
                    'required_with:subject',
                    'exists:users,id',
                ],
            ];
    }
}
