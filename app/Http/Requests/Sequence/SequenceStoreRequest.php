<?php

namespace App\Http\Requests\Sequence;

use App\Models\Sequence\Sequence;
use App\Rules\ValidateSequenceEntity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SequenceStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'name' => [
                    'string',
                    'required',
                ],
                'startDate' => [
                    'required',
                    'date',
                ],
                'isActive' => [
                    'boolean',
                ],
                'templates' => [
                    'array',
                    'required',
                    "max:6",
                ],
                'templates.*.templateId' => [
                    'int',
                    'exists:template,id',
                ],
                'templates.*.sendAfter' => [
                    'int',
                ],
                'templates.*.sendAfterUnit' => [
                    Rule::in([Sequence::SEND_AFTER_UNIT_DAY, Sequence::SEND_AFTER_UNIT_MONTH]),
                ],
                'entity' => [
                    'array',
                    'required',
                ],
                'entity.*' => [
                    new ValidateSequenceEntity(),
                ],
            ];
    }
}
