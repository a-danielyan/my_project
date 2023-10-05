<?php

namespace App\Http\Requests\ReferenceTables\Stage;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StageUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $stage = $this->route('stage');

        return
            [
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('solution')->where(function ($query) use ($stage) {
                        return $query->whereNull('deleted_at')->where('id', '!=', $stage->getKey());
                    }),
                ],
                'status' => [
                    'required',
                    Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE]),
                ],
                'sortOrder' => [
                    'integer',
                ],
            ];
    }
}
