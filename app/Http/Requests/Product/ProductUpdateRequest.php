<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\Traits\CustomFieldValidationTrait;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
{
    use CustomFieldValidationTrait;

    public function rules(): array
    {
        return array_merge(
            [
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
            ],
            $this->customFieldValidation(Product::class, true),
        );
    }
}
