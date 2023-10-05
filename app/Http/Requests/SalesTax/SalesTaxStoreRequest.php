<?php

namespace App\Http\Requests\SalesTax;

use App\Models\SalesTax;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalesTaxStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return
            [
                'stateCode' => [
                    'required',
                    'string',
                    Rule::in(SalesTax::AVAILABLE_STATE_CODES),
                    'unique:sales_taxes,state_code',
                ],
                'tax' => [
                    'required',
                    'decimal:0,2',
                ],
            ];
    }
}
