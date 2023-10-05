<?php

namespace App\Http\Requests\Oauth2;

use App\Http\Requests\BaseFormRequest;

class Oauth2TokenDeleteRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
