<?php

namespace App\Http\Requests\Config;

use App\Http\Requests\BaseGetFormRequest;

class CitySearchRequest extends BaseGetFormRequest
{
    /**
     * @return string[][]
     */
    public function rules(): array
    {
        return [
            'search' => [
                'string',
                'required',
                'min:2',
            ],
        ];
    }
}
