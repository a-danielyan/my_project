<?php

namespace App\Http\Requests\ReferenceTables\LeadType;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadTypeUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $leadType = $this->route('leadType');

        return
            [
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('lead_type')->where(function ($query) use ($leadType) {
                        return $query->whereNull('deleted_at')->where('id', '!=', $leadType->getKey());
                    }),
                ],
                'status' => [
                    'required',
                    Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE]),
                ],

            ];
    }
}
