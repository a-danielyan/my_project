<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Rules\AlphaSpace;
use App\Rules\AlphaSpaceHyphen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return array_merge_recursive(
            [
                'firstName' => [
                    new AlphaSpace(),
                    'min:1',
                    'max:50',
                ],
                'lastName' => [
                    new AlphaSpaceHyphen(),
                    'min:1',
                    'max:50',
                ],
                'email' => [
                    'email',
                    "unique:users,email,$user->id,id,deleted_at,NULL",
                ],
                'roleId' => [
                    'int',
                    Rule::exists('role', 'id'),
                ],
                'phone' => [
                    'phone:AUTO',
                ],
                'status' => [
                    'string',
                    Rule::in(User::AVAILABLE_STATUSES),
                ],
            ],
        );
    }
}
