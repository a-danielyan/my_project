<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\BaseGetFormRequest;

class RoleGetRequest extends BaseGetFormRequest
{
    /**
     * Get the validation rules that apply to the request
     *
     * @return array
     */
    public function rules(): array
    {
        return array_merge_recursive(
            parent::rules(),
            [
                'description' => [
                    'string',
                ],
            ]
        );
    }
}
