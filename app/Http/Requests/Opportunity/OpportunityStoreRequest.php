<?php

namespace App\Http\Requests\Opportunity;

use App\Http\Requests\Traits\CustomFieldValidationTrait;
use App\Models\Opportunity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OpportunityStoreRequest extends FormRequest
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
                'projectName' => [
                    'string',
                ],
                'projectType' => [
                    'string',
                    Rule::in([Opportunity::EXISTED_BUSINESS, Opportunity::NEW_BUSINESS]),
                ],
                'stageId' => [
                    'required',
                    'int',
                    'exists:stage,id',
                ],
                'expectingClosingDate' => [
                    'date',
                ],
                'expectedRevenue' => [
                    'decimal:0,2',
                ],
                'accountId' => [
                    'required',
                    'exists:account,id',
                ],
                'internalNotes' => [
                    'nullable',
                    'string',
                ],
            ],
            $this->customFieldValidation(Opportunity::class),
        );
    }
}
