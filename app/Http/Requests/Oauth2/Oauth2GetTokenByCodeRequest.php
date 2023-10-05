<?php

namespace App\Http\Requests\Oauth2;

use Illuminate\Foundation\Http\FormRequest;

class Oauth2GetTokenByCodeRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
            ],
            'userName' => [
                'nullable',
                'string',
            ],
        ];
    }
}
