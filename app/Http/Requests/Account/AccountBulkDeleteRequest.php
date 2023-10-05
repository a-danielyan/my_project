<?php

namespace App\Http\Requests\Account;

use App\Rules\NotEmptyStringIdsList;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AccountBulkDeleteRequest
 * @property-read string ids
 */
class AccountBulkDeleteRequest extends FormRequest
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
