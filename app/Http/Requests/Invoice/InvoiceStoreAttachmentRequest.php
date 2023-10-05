<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceStoreAttachmentRequest extends FormRequest
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
