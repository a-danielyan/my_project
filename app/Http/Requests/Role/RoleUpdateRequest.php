<?php

namespace App\Http\Requests\Role;

use App\Rules\AlphaSpace;
use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'name' => [
                    new AlphaSpace(),
                    'max:50',
                ],
                'description' => [
                    new AlphaSpace(),
                    'max:50',
                ],

            ];
    }
}
