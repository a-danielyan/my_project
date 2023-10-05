<?php

namespace App\Http\Requests\Estimate;

use App\Rules\NotEmptyStringIdsList;
use Illuminate\Foundation\Http\FormRequest;

class EstimateBulkDeleteRequest extends FormRequest
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
                new NotEmptyStringIdsList(),
            ],
        ];
    }
}
