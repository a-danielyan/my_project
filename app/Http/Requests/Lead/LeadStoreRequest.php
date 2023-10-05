<?php

namespace App\Http\Requests\Lead;

use App\Http\Requests\Traits\CustomFieldValidationTrait;
use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadStoreRequest extends FormRequest
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
                'customFields' => [
                    'array',
                ],
                'tag' => [
                    'array',
                ],
                'tag.*.id' => [
                    'int',
                    Rule::exists('tag', 'id')->where('entity_type', Lead::class),
                ],
                'internalNotes' => [
                    'nullable',
                    'string',
                ],
            ],
            $this->customFieldValidation(Lead::class),
        );
    }
}
