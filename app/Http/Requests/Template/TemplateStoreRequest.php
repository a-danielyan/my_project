<?php

namespace App\Http\Requests\Template;

use App\Models\Template;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TemplateStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'entity' => [
                    'required',
                    Rule::in(Template::AVAILABLE_TEMPLATE_TYPES),
                ],
                'template' => [
                    'string',
                ],
                'isDefault' => [
                    'boolean',
                ],
                'isShared' => [
                    'boolean',
                ],
                'tag' => [
                    'array',
                ],
                'tag.*.id' => [
                    'int',
                    Rule::exists('tag', 'id')->where('entity_type', Template::class),
                ],
                'name' => [
                    'string',
                ],
                'status' => [
                    'string',
                ],
                'thumbImage' => [
                    'file',
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
            'isDefault' => $this->toBoolean($this->isDefault),
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
