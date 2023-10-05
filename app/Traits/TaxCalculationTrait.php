<?php

namespace App\Traits;

use App\Models\Account;
use App\Models\CustomFieldValues;
use App\Models\SalesTax;

trait TaxCalculationTrait
{
    protected function calculateTax(float $price, string $state, int $accountId): float
    {
        $tax = 0.00;

        /** @var CustomFieldValues $accountSalesTax */
        $accountSalesTax = CustomFieldValues::query()->where('entity', Account::class)
            ->where('entity_id', $accountId)->whereHas('customField', function ($query) {
                $query->where('entity_type', Account::class)->where('code', 'sales-tax');
            })->first();

        if ($accountSalesTax && $accountSalesTax->boolean_value === 0) {
            return $tax;
        }

        $stateCode = SalesTax::AVAILABLE_STATE_CODES[$state] ?? $state;
        /** @var SalesTax $salesTax */
        $salesTax = SalesTax::query()->where('state_code', $stateCode)->first();

        if ($salesTax) {
            $tax = ($price * $salesTax->tax) / 100;
        }

        return round($tax, 2);
    }
}
