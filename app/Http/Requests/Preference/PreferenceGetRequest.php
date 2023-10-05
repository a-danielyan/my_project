<?php

namespace App\Http\Requests\Preference;

use Illuminate\Foundation\Http\FormRequest;

class PreferenceGetRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'entity' => [
                'required',
                'string',
            ],
        ];
    }
}
