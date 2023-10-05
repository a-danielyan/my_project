<?php

namespace App\Http\Requests\TermsAndConditions;

use App\Models\TermsAndConditions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TermsAndConditionGetRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'entity' => [
                'required',
                'string',
                Rule::in(
                    [
                        TermsAndConditions::ESTIMATE_ENTITY,
                        TermsAndConditions::INVOICE_ENTITY,
                        TermsAndConditions::PROPOSAL_ENTITY,
                    ],
                ),
            ],
        ];
    }
}
