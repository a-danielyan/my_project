<?php

namespace App\Http\Requests;

class AuthorizeNetTransactionRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'opaqueDataDescriptor' => [
                'required',
                'string',
            ],
            'opaqueDataValue' => [
                'required',
                'string',
            ],
        ];
    }
}
