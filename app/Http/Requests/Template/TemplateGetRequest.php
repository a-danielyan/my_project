<?php

namespace App\Http\Requests\Template;

use App\Http\Requests\BaseGetFormRequest;

class TemplateGetRequest extends BaseGetFormRequest
{
    protected const MULTI_SELECT_FIELDS = [
        'entity',
        'status',
        'isDefault',
        'tag',
        'name',
    ];

    protected function prepareForValidation(): void
    {
        $this->merge([
            'isDefault' => $this->toBoolean($this->isDefault),
            'isShared' => $this->toBoolean($this->isShared),
        ]);
        parent::prepareForValidation();
    }

    /**
     * Convert to boolean
     *
     * @param $param
     * @return ?boolean
     */
    private function toBoolean($param): ?bool
    {
        if ($param === null) {
            return null;
        }

        return filter_var($param, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
