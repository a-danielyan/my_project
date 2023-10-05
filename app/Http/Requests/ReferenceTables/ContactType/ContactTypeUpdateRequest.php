<?php

namespace App\Http\Requests\ReferenceTables\ContactType;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactTypeUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $accountPartnership = $this->route('contactType');

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
