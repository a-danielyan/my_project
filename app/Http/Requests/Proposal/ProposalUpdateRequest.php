<?php

namespace App\Http\Requests\Proposal;

use App\Http\Requests\Traits\CustomFieldValidationTrait;
use App\Models\Proposal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProposalUpdateRequest extends FormRequest
{
    use CustomFieldValidationTrait;

    public function rules(): array
    {
        return
            [
                'opportunityId' => [
                    'exists:opportunity,id',
                ],
                'status' => [
                    Rule::in(
                        Proposal::PROPOSAL_STATUS_CREATED,
                        Proposal::PROPOSAL_STATUS_ACCEPTED,
                        Proposal::PROPOSAL_STATUS_OPENED,
                        Proposal::PROPOSAL_STATUS_SENT,
                    ),
                ],
                'templateId' => [
                    'required',
                    'exists:template,id',
                ],
                'estimates' => [
                    'array',
                ],
                'estimates.*' => [
                    'int',
                    'exists:estimate,id',
                ],
            ];
    }
}
