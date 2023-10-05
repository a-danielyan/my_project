<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class ContactStoreAttachmentRequest extends FormRequest
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
