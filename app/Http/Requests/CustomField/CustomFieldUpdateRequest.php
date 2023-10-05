<?php

namespace App\Http\Requests\CustomField;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CustomFieldUpdateRequest extends CustomFieldStoreRequest
{
    public function rules(): array
    {
        return
            array_merge(
                parent::rules(),
                $this->getNameValidationRule(),
            );
    }

    private function getNameValidationRule(): array
    {
        $request = request();
        $customField = $this->route('customField');

        return [
            'name' => [
                'required',
                'string',
                Rule::unique('custom_field')->where(function ($query) use ($request, $customField) {
                    return $query->where('entity_type', $request->entityType)
                        ->where('code', Str::slug($request->name))
                        ->where('id', '!=', $customField->id)
                        ->whereNull('deleted_at');
                }),

            ],
        ];
    }
}
