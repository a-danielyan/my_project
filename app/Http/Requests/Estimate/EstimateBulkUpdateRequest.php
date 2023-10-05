<?php

namespace App\Http\Requests\Estimate;

use App\Models\Estimate;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstimateBulkUpdateRequest extends FormRequest
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
                Rule::exists('tag', 'id')->where('entity_type', Estimate::class),
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
