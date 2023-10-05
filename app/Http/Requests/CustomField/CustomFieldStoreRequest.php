<?php

namespace App\Http\Requests\CustomField;

use App\Http\Controllers\EntityLogController;
use App\Models\CustomField;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CustomFieldStoreRequest extends FormRequest
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
                    'type' => [
                        'required',
                        Rule::in(CustomField::AVAILABLE_ENTITY_TYPES),
                    ],
                    'lookupType' => [
                        'string',
                        Rule::in(CustomField::AVAILABLE_LOOKUP_TYPES),
                    ],
                    'sortOrder' => [
                        'integer',
                    ],
                    'isRequired' => [
                        'boolean',
                    ],
                    'isMultiple' => [
                        'boolean',
                    ],
                    'isUnique' => [
                        'boolean',
                    ],
                    'parentId' => [
                        'integer',
                        'exists:custom_field,id',
                    ],
                ],
                $this->getNameValidationRule(),
            );
    }

    private function getNameValidationRule(): array
    {
        $request = request();

        return [
            'name' => [
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
