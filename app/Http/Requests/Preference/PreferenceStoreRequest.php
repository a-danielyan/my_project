<?php

namespace App\Http\Requests\Preference;

use Illuminate\Foundation\Http\FormRequest;

class PreferenceStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'entity' => [
                'required',
                'string',
            ],
            'name' => [
                'required',
                'string',
            ],
            'settings' => [
                'required',
            ],
        ];
    }
}
