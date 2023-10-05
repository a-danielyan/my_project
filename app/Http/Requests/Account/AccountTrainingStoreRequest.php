<?php

namespace App\Http\Requests\Account;

use App\Models\Activity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountTrainingStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'trainingDate' => [
                    'required',
                    'date',
                ],
                'dueDate' => [
                    'required_with:subject',
                    'date',
                ],
                'trainedBy' => [
                    'required',
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
