<?php

namespace App\Http\Requests\ReferenceTables\Industry;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndustryStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('industries')->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ],
                'status' => [
                    'required',
                    Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE]),
                ],

            ];
    }
}
