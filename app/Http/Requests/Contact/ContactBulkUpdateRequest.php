<?php

namespace App\Http\Requests\Contact;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactBulkUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'ids' => [
                'required',
            ],
            'customFields' => [
                'array',
            ],
            'tag' => [
                'array',
            ],
            'tag.*.id' => [
                'int',
                Rule::exists('tag', 'id')->where('entity_type', Contact::class),
            ],
            'status' => [
                'string',
                Rule::in([
                    User::STATUS_ACTIVE,
                    User::STATUS_INACTIVE,
                ]),
            ],
            'accountId' => [
                'int',
                'exists:account,id',
            ],
            'salutation' => [
                Rule::in(Lead::AVAILABLE_SALUTATION),
            ],
        ];
    }
}
