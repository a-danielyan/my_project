<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class HandleProviderCallbackRequest
 * @package App\Http\Requests\Common\Auth
 */
class HandleProviderCallbackRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $providers = [
            'google',
            'local',
        ];

        return [
            'provider' => ['required', Rule::in($providers)],
            'token' => 'required_without_all:code,tokenId',
            'code' => 'required_without_all:token,tokenId',
            'tokenId' => 'required_without_all:token,code',
        ];
    }
}
