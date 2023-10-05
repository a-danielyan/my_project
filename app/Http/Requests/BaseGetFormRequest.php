<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\MultiSelectTrait;

abstract class BaseGetFormRequest extends BaseFormRequest
{
    use MultiSelectTrait;

    protected const DEFAULT_LIMIT = 10;

    protected const MULTI_SELECT_FIELDS = [
        'status'
    ];

    /**
     * @param array|mixed|null $keys
     * @return array
     */
    public function all($keys = null): array
    {
        $data = parent::all($keys);

        $data['limit'] = $data['limit'] ?? self::DEFAULT_LIMIT;

        return $data;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'limit' => [
                'int',
                'min:1',
            ],
            'page' => [
                'int',
                'min:1',
            ],
            'fields' => 'string',
            'distinct' => 'nullable',
            'exact' => 'nullable',
            'id' => [
                'int',
            ],
            'name' => [
                'string',
                'min:1',
            ],
            'search' => [
                'string',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->prepareMultiSelectForValidation();
    }
}
