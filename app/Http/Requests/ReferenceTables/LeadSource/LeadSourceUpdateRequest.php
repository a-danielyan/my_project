<?php

namespace App\Http\Requests\ReferenceTables\LeadSource;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadSourceUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $leadSource = $this->route('leadSource');

        return
            [
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('lead_source')->where(function ($query) use ($leadSource) {
                        return $query->whereNull('deleted_at')->where('id', '!=', $leadSource->getKey());
                    }),
                ],
                'status' => [
                    'required',
                    Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE]),
                ],

            ];
    }
}
