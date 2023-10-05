<?php

namespace App\Http\Requests\Opportunity;

use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OpportunityBulkUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'ids' => [
                'required',
            ],
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
            'status' => [
                'string',
                Rule::in([
                    User::STATUS_ACTIVE,
                    User::STATUS_INACTIVE,
                ]),
            ],
            "projectName" => [
                'nullable',
                'string',
            ],
            "stageId" => [
                'nullable',
                'exists:stage,id',
            ],
            "projectType" => [
                'nullable',
                'string',
                Rule::in([Opportunity::EXISTED_BUSINESS, Opportunity::NEW_BUSINESS]),
            ],
            "expectingClosingDate" => [
                'nullable',
                'date',
            ],
        ];
    }
}
