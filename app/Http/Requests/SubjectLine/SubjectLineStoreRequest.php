<?php

namespace App\Http\Requests\SubjectLine;

use Illuminate\Foundation\Http\FormRequest;

class SubjectLineStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'subjectText' => [
                    'required',
                    'string',
                ],
            ];
    }
}
