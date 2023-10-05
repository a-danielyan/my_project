<?php

namespace App\Http\Requests\Email;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmailSentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'subject' => [
                'required',
                'string',
            ],
            'message' => [
                'required',
                'string',
            ],
            'sendTo' => [
                'required',
                'array',
            ],
            'sendTo.*' => [
                'email',
            ],
            'cc' => [
                'array',
            ],
            'cc.*' => [
                'email',
            ],
            'bcc' => [
                'array',
            ],
            'bcc.*' => [
                'email',
            ],
            'relatedToEntity' => [
                'nullable',
                'string',
                Rule::in(['Lead', 'Contact', 'Account']),
            ],
            'relatedToId' => [
                'nullable',
                'int',
            ],
            'attachments' => [
                'array',
            ],
            'attachments.*' => [
                'file',
            ],
            'sendAt' => [
                'nullable',
                'date',
            ],
        ];
    }
}
