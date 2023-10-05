<?php

namespace App\Http\Requests\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductBulkUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'ids' => [
                'required',
            ],
            'customFields' => [
                'array',
            ],
            'tag' => [
                'array',
            ],
            'tag.*.id' => [
                'int',
                Rule::exists('tag', 'id')->where('entity_type', Product::class),
            ],
            'status' => [
                'string',
                Rule::in([
                    User::STATUS_ACTIVE,
                    User::STATUS_INACTIVE,
                ]),
            ],
        ];
    }
}
