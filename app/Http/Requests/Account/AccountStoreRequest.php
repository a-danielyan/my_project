<?php

namespace App\Http\Requests\Account;

use App\Http\Requests\Traits\CustomFieldValidationTrait;
use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

class AccountStoreRequest extends FormRequest
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
                'internalNotes' => [
                    'nullable',
                    'string',
                ],
            ],
            $this->customFieldValidation(Account::class),
        );
    }
}
