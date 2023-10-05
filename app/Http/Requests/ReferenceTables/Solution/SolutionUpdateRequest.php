<?php

namespace App\Http\Requests\ReferenceTables\Solution;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SolutionUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $solution = $this->route('solution');

        return
            [
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('solution')->where(function ($query) use ($solution) {
                        return $query->whereNull('deleted_at')->where('id', '!=', $solution->getKey());
                    }),
                ],
                'status' => [
                    'required',
                    Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE]),
                ],

            ];
    }
}
