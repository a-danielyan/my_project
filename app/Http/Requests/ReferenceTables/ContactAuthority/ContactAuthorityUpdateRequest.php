<?php

namespace App\Http\Requests\ReferenceTables\ContactAuthority;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactAuthorityUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $accountPartnership = $this->route('contactAuthority');

        return
            [
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('account_partnership_status')->where(function ($query) use ($accountPartnership) {
                        return $query->whereNull('deleted_at')->where('id', '!=', $accountPartnership->getKey());
                    }),
                ],
                'status' => [
                    'required',
                    Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE]),
                ],

            ];
    }
}
