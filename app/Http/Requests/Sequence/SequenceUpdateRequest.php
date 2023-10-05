<?php

namespace App\Http\Requests\Sequence;

use App\Models\Sequence\Sequence;
use App\Rules\ValidateSequenceEntity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SequenceUpdateRequest extends FormRequest
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
                ],
                'entity.*' => [
                    new ValidateSequenceEntity(),
                ],
            ];
    }
}
