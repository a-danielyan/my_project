<?php

namespace App\Http\Requests\ReferenceTables\LeadStatus;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadStatusUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $leadStatus = $this->route('leadStatus');

        return
            [
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('lead_status')->where(function ($query) use ($leadStatus) {
                        return $query->whereNull('deleted_at')->where('id', '!=', $leadStatus->getKey());
                    }),
                ],
                'status' => [
                    'required',
                    Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE]),
                ],

            ];
    }
}
