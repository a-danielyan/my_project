<?php

namespace App\Http\Requests\Role;

use App\Rules\AlphaSpace;
use Illuminate\Foundation\Http\FormRequest;

class RoleStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'name' => [
                    'required',
                    new AlphaSpace(),
                    'max:50',
                ],
                'description' => [
                    'required',
                    new AlphaSpace(),
                    'max:50',
                ],

            ];
    }
}
