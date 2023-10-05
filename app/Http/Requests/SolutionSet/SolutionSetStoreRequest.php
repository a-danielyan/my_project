<?php

namespace App\Http\Requests\SolutionSet;

use Illuminate\Foundation\Http\FormRequest;

class SolutionSetStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'name' => [
                    'string',
                ],
                'items' => [
                    'array',
                ],
                'items.*.productId' => [
                    'required',
                    'exists:product,id',
                ],
                'items.*.quantity' => [
                    'required',
                    'decimal:0,2',
                ],
                'items.*.price' => [
                    'required',
                    'decimal:0,2',
                ],
                'items.*.discount' => [
                    'decimal:0,2',
                ],
                'items.*.description' => [
                    'string',
                ],
            ];
    }
}
