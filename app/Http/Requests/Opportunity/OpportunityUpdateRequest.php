<?php

namespace App\Http\Requests\Opportunity;

use App\Http\Requests\Traits\CustomFieldValidationTrait;
use App\Models\Opportunity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OpportunityUpdateRequest extends FormRequest
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
                    Rule::exists('tag', 'id')->where('entity_type', Opportunity::class),
                ],
                'expectedRevenue' => [
                    'decimal:0,2',
                ],
                'accountId' => [
                    'exists:account,id',
                ],
                'internalNotes' => [
                    'nullable',
                    'string',
                ],
            ],
            $this->customFieldValidation(Opportunity::class, true),
        );
    }
}
