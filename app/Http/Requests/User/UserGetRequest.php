<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseGetFormRequest;
use App\Models\User;
use App\Rules\AlphaSpace;
use App\Rules\AlphaSpaceHyphen;

class UserGetRequest extends BaseGetFormRequest
{
    protected const MULTI_SELECT_FIELDS = [
        'status',
        'roleId',
    ];

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
                'firstName' => [
                    new AlphaSpace(),
                ],
                'lastName' => [
                    new AlphaSpaceHyphen(),
                ],
                'email' => [
                    'string',
                    'min:1',
                ],
                'status' => [
                    'array',
                ],
                'status.*' => [
                    'in:' . implode(',', User::AVAILABLE_STATUSES),
                ],
                'fullName' => [
                    new AlphaSpace(),
                ],
            ],
        );
    }
}
