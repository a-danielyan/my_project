<?php

namespace App\Http\Requests\Oauth2;

use App\Http\Requests\BaseGetFormRequest;

class Oauth2GetRequest extends BaseGetFormRequest
{
    public function rules(): array
    {
        return array_merge_recursive(
            parent::rules(),
            [
                'service' => [
                    'string',
                    'required',
                ],
            ],
        );
    }
}
