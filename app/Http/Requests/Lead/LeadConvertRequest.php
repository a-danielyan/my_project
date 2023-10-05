<?php

namespace App\Http\Requests\Lead;

use App\Http\Services\LeadToAccountContactConvertService;
use App\Models\Opportunity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadConvertRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'actionAccount' => [
                    'nullable',
                    Rule::in([
                        LeadToAccountContactConvertService::ACTION_ADD_TO_EXISTING,
                        LeadToAccountContactConvertService::ACTION_CREATE_NEW,
                    ]),
                ],
                'actionContact' => [
                    'nullable',
                    Rule::in([
                        LeadToAccountContactConvertService::ACTION_ADD_TO_EXISTING,
                        LeadToAccountContactConvertService::ACTION_CREATE_NEW,
                    ]),
                ],
                'actionOpportunity' => [
                    'nullable',
                    Rule::in([
                        LeadToAccountContactConvertService::ACTION_ADD_TO_EXISTING,
                        LeadToAccountContactConvertService::ACTION_CREATE_NEW,
                    ]),
                ],
                'doNotCreateOpportunity' => [
                    'nullable',
                    'boolean',
                ],
                'accountCustomFields' => [
                    'array',
                    'required_if:actionAccount,' . LeadToAccountContactConvertService::ACTION_ADD_TO_EXISTING,
                ],
                'contactCustomFields' => [
                    'array',
                    'required_if:actionContact,' . LeadToAccountContactConvertService::ACTION_ADD_TO_EXISTING,
                ],
                'opportunityCustomFields' => [
                    'array',
                    'required_if:actionOpportunity,' . LeadToAccountContactConvertService::ACTION_CREATE_NEW,
                ],
                'opportunityCustomFields.opportunityName' => [
                    'string',
                    'nullable',
                ],
                'opportunityCustomFields.expectedClosingDate' => [
                    'date',
                    'nullable',
                ],
                'opportunityCustomFields.stageId' => [
                    'nullable',
                    'exists:stage,id',
                ],
                'opportunityCustomFields.projectType' => [
                    'nullable',
                    Rule::in([Opportunity::EXISTED_BUSINESS, Opportunity::NEW_BUSINESS]),
                ],
                'opportunityCustomFields.solutionInterest' => [
                    'nullable',
                    'exists:solution,id',
                ],
                'opportunityCustomFields.contactAuthority' => [
                    'nullable',
                    'array',
                ],
                'opportunityCustomFields.contactAuthority.*' => [
                    'exists:contact_authority,id',
                ],
                'selectedOpportunityId' => [
                    'exists:opportunity,id',
                    'required_if:actionOpportunity,' . LeadToAccountContactConvertService::ACTION_ADD_TO_EXISTING,
                ],
            ];
    }

    /**
     * Prepare inputs for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'doNotCreateOpportunity' => $this->toBoolean($this->doNotCreateOpportunity),
        ]);
    }

    /**
     * Convert to boolean
     *
     * @param $param
     * @return boolean
     */
    private function toBoolean($param): bool
    {
        return filter_var($param, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
