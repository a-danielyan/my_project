<?php

namespace App\Http\Requests\Estimate;

use App\Http\Requests\Traits\CustomFieldValidationTrait;
use App\Models\Estimate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstimateUpdateRequest extends FormRequest
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
                    Rule::exists('tag', 'id')->where('entity_type', Estimate::class),
                ],
                'itemGroups' => [
                    'array',
                ],
                'itemGroups.*.contactId' => [
                    'nullable',
                    'exists:contact,id',
                ],
                'itemGroups.*.address' => [
                    $this->checkAddresRequired(),
                    'array',
                ],
                'itemGroups.*.items' => [
                    'required',
                    'array',
                ],
                'itemGroups.*.items.*.productId' => [
                    'required',
                    'exists:product,id',
                ],
                'itemGroups.*.items.*.quantity' => [
                    'required',
                    'numeric',
                ],
                'itemGroups.*.items.*.description' => [
                    'string',
                ],
                'itemGroups.*.items.*.combinePrice' => [
                    'boolean',
                    'nullable',
                ],
                'itemGroups.*.items.*.childItems' => [
                    'array',
                    'nullable',
                ],
                'itemGroups.*.items.*.tax_percent' => [
                    'decimal:0,2',
                    'nullable',
                ],
                'estimateName' => [
                    'string',
                ],
                'status' => [
                    'string',
                    Rule::in(Estimate::AVAILABLE_STATUSES),
                ],
                'estimateValidityDuration' => [
                    'date',
                ],
                'discountPercent' => [
                    'nullable',
                    'decimal:0,2',
                ],
            ],
            $this->customFieldValidation(Estimate::class, true),
        );
    }

    private function checkAddresRequired(): string
    {
        if (isset($this['customFields']['ship-to-multiple']) && $this['customFields']['ship-to-multiple']) {
            return 'required';
        }

        return 'nullable';
    }
}
