<?php

namespace App\Http\Requests\Contact;

use App\Http\Requests\Traits\CustomFieldValidationTrait;
use App\Models\Contact;
use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactStoreRequest extends FormRequest
{
    use CustomFieldValidationTrait;

    public function rules(): array
    {
        return array_merge(
            [
                'salutation' => [
                    'required',
                    Rule::in(Lead::AVAILABLE_SALUTATION),
                ],
                'accountId' => [
                    'required',
                    'int',
                    'exists:account,id',
                ],
                'customFields' => [
                    'array',
                ],
                'tag' => [
                    'array',
                ],
                'avatarFile' => [
                    'file',
                ],
                'avatar' => [
                    'string',
                ],
            ],
            $this->customFieldValidation(Contact::class),
        );
    }
}
