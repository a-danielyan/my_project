<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class BaseFormRequest
 * @package App\Http\Requests
 */
abstract class BaseFormRequest extends FormRequest
{
    /**
     * @return User
     */
    public function getAuthUser(): User
    {
        return auth()->user();
    }
}
