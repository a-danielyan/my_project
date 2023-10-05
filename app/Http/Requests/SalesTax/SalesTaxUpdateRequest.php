<?php

namespace App\Http\Requests\SalesTax;

use App\Models\SalesTax;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalesTaxUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $salesTax = $this->route('sales_tax');

        return
            [
                'stateCode' => [
                    'string',
                    Rule::in(SalesTax::AVAILABLE_STATE_CODES),
                    Rule::unique('sales_taxes', 'state_code')->where(function ($query) use ($salesTax) {
                        return $query->whereNull('deleted_at')->where('id', '!=', $salesTax->getKey());
                    }),
                ],
                'tax' => [
                    'decimal:0,2',
                ],
            ];
    }
}
