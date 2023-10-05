<?php

namespace App\Http\Requests\ReferenceTables\LeadType;

use App\Http\Requests\BaseGetFormRequest;

class LeadTypeGetRequest extends BaseGetFormRequest
{
    /**
     * Get the validation rules that apply to the request
     *
     * @return array
     */
    public function rules(): array
    {
        return array_merge_recursive(
            parent::rules(),
            [
                'description' => [
                    'string',
                ],
            ],
        );
    }
}
