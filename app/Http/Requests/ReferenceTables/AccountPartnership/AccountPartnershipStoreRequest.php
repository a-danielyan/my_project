<?php

namespace App\Http\Requests\ReferenceTables\AccountPartnership;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountPartnershipStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('lead_type')->where(function ($query) {
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
