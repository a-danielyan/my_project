<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SendResetEmailRequest
 * @package App\Http\Requests\Common\Auth
 * @property-read string email
 */
class SendResetEmailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
        ];
    }
}
