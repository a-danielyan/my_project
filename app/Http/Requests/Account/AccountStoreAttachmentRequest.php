<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class AccountStoreAttachmentRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'file' => [
                    'file',
                ],
                'link' => [
                    'string',
                ],
                'name' => [
                    'string',
                ],
            ];
    }
}
