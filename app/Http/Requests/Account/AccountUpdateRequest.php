<?php

namespace App\Http\Requests\Account;

use App\Http\Requests\Traits\CustomFieldValidationTrait;
use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountUpdateRequest extends FormRequest
{
    use CustomFieldValidationTrait;

    public function rules(): array
    {
        return array_merge(
            [
                'customFields' => [
                    'array',
                ],
                'tag' => [
                    'array',
                ],
                'tag.*.id' => [
                    'int',
                    Rule::exists('tag', 'id')->where('entity_type', Account::class),
                ],
                'cmsClientId' => [
                    'nullable',
                    'numeric',
                ],
                'parentAccountId' => [
                    'nullable',
                    'exists:account,id',
                ],
                'accountsPayable' => [
                    'nullable',
                    'array',
                ],
                'accountsPayable.*' => [
                    'exists:contact,id',
                ],
                'leadId' => [
                    'nullable',
                    'exists:lead,id',
                ],
                'status' => [
                    'string',
                ],
                'avatarFile' => [
                    'file',
                ],
                'avatar' => [
                    'string',
                ],
                'internalNotes' => [
                    'nullable',
                    'string',
                ],
            ],
            $this->customFieldValidation(Account::class, true),
        );
    }
}
