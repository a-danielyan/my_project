<?php

namespace App\Http\Requests\Traits;

use App\Models\CustomField;
use Illuminate\Validation\Rule;

trait CustomFieldValidationTrait
{
    private function customFieldValidation(string $entityType, bool $update = false): array
    {
        $allCustomFields = CustomField::query()->where('entity_type', $entityType)->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->code => [
                        'type' => $item->type,
                        'lookup_type' => $item->lookup_type,
                        'is_required' => $item->is_required,
                        'is_unique' => $item->is_unique,
                        'is_multiple' => $item->is_multiple,
                        'id' => $item->id,
                    ],
                ];
            })->toArray();

        $rules = [];
        foreach ($allCustomFields as $code => $customField) {
            $fieldRule = [];

            if ($customField['is_required'] && !$update && $customField['type'] !== CustomField::FIELD_TYPE_INTERNAL) {
                $fieldRule[] = 'required';
            } else {
                $fieldRule[] = 'nullable';
            }

            switch ($customField['type']) {
                case CustomField::FIELD_TYPE_EMAIL:
                    $fieldRule[] = 'email';
                    break;

                case CustomField::FIELD_TYPE_TEXT:
                case CustomField::FIELD_TYPE_TEXTAREA:
                case CustomField::FIELD_TYPE_PHONE:
                case CustomField::FIELD_TYPE_CHECKBOX:
                    $fieldRule[] = 'string';
                    break;

                case CustomField::FIELD_TYPE_SELECT:
                    $fieldRule[] = 'integer';
                    $fieldRule[] = Rule::exists('custom_field_option', 'id')->where(
                        'custom_field_id',
                        $customField['id'],
                    );
                    break;

                case CustomField::FIELD_TYPE_MULTISELECT:
                case CustomField::FIELD_TYPE_JSON:
                    $fieldRule[] = 'array';
                    break;

                case CustomField::FIELD_TYPE_DATETIME:
                case CustomField::FIELD_TYPE_DATE:
                    $fieldRule[] = 'date';
                    break;

                case CustomField::FIELD_TYPE_PRICE:
                    $fieldRule[] = 'decimal:0,2';
                    break;

                case CustomField::FIELD_TYPE_BOOL:
                    $fieldRule[] = 'boolean';
                    break;
                case CustomField::FIELD_TYPE_NUMBER:
                    $fieldRule[] = 'numeric';
                    break;

                case CustomField::FIELD_TYPE_FILE:
                case CustomField::FIELD_TYPE_IMAGE:
                    $fieldRule[] = 'file';
                    break;

                case CustomField::FIELD_TYPE_INTERNAL:
                case CustomField::FIELD_TYPE_CONTAINER:
                    $fieldRule[] = 'prohibited';
                    break;

                case CustomField::FIELD_TYPE_LOOKUP:
                    $fieldRule[] = 'sometimes';
                    $fieldRule[] = 'required';
                    $fieldRule[] = 'exists:' . $customField['lookup_type'] . ',id';
                    break;
            }

            $rules['customFields.' . $code] = $fieldRule;
        }

        return $rules;
    }
}
