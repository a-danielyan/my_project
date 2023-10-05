<?php

namespace App\Http\Requests\License;

use App\Models\License;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LicenseStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'name' => [
                    'required',
                    'string',
                ],
                'licenseDurationInMonth' => [
                    'required',
                    'integer',
                ],
                'licenseType' => [
                    'required',
                    Rule::in(License::AVAILABLE_LICENSE_TYPES),
                ],
                'tag' => [
                    'array',
                ],
            ];
    }
}
