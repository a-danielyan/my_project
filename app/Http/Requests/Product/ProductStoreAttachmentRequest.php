<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreAttachmentRequest extends FormRequest
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
