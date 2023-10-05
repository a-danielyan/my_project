<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;

class DeviceSyncRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'devices' => [
                    'required',
                    'array',
                ],
            ];
    }
}
