<?php

namespace App\Http\Requests\ReferenceTables\Industry;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndustryUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $industry = $this->route('industry');

        return
            [
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('industries')->where(function ($query) use ($industry) {
                        return $query->whereNull('deleted_at')->where('id', '!=', $industry->getKey());
                    }),
                ],
                'status' => [
                    'required',
                    Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE]),
                ],

            ];
    }
}
