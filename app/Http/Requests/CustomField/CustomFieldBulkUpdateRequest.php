<?php

namespace App\Http\Requests\CustomField;

use App\Http\Controllers\EntityLogController;
use App\Models\CustomField;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CustomFieldBulkUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return
            array_merge(
                [
                    'entityType' => [
                        'required',
                        Rule::in(EntityLogController::ALLOWED_ENTITY),
                    ],
                    'fields' => [
                        'array',
                    ],
                    'fields.*.type' => [
                        'required',
                        Rule::in(CustomField::AVAILABLE_ENTITY_TYPES),
                    ],
                    'fields.*.lookupType' => [
                        Rule::in(CustomField::AVAILABLE_LOOKUP_TYPES),
                        'nullable',
                    ],
                    'fields.*.sortOrder' => [
                        'required',
                        'integer',
                    ],
                    'fields.*.isRequired' => [
                        'boolean',

                    ],
                    'fields.*.isUnique' => [
                        'boolean',
                    ],
                    'fields.*.isMultiple' => [
                        'boolean',
                    ],
                    'fields.*.parentId' => [
                        'integer',
                        'exists:custom_field,id',
                    ],
                    'fields.*.childs' => [
                        'array',
                    ],
                    'fields.*.width' => [
                        'nullable',
                        'decimal:0,1',
                    ],
                    'fields.*.tooltip' => [
                        'nullable',
                        'string',
                    ],
                    'fields.*.tooltipType' => [
                        'nullable',
                        'string',
                    ],
                    'fields.*.property' => [
                        'nullable',
                        'array',
                    ],

                ],
                $this->getNameValidationRule(),
            );
    }

    private function getNameValidationRule(): array
    {
        $request = request();

        return [
            'fields.*.name' => [
                'required',
                'string',
                Rule::unique('custom_field')->where(function ($query) use ($request) {
                    return $query->where('entity_type', $request->entityType)
                        ->where('code', Str::slug($request->name))
                        ->whereNull('deleted_at');
                }),
            ],
        ];
    }
}
