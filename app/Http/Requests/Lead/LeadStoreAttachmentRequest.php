<?php

namespace App\Http\Requests\Lead;

use Illuminate\Foundation\Http\FormRequest;

class LeadStoreAttachmentRequest extends FormRequest
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
