<?php

namespace App\Http\Requests\Contact;

use App\Http\Requests\Traits\CustomFieldValidationTrait;
use App\Models\Contact;
use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactUpdateRequest extends FormRequest
{
    use CustomFieldValidationTrait;

    public function rules(): array
    {
        return array_merge(
            [
                'salutation' => [
                    Rule::in(Lead::AVAILABLE_SALUTATION),
                ],
                'accountId' => [
                    'int',
                    'exists:account,id',
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
                'avatarFile' => [
                    'file',
                ],
                'avatar' => [
                    'string',
                ],
            ],
            $this->customFieldValidation(Contact::class, true),
        );
    }
}
