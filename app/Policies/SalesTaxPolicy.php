<?php

namespace App\Policies;

use App\Models\SalesTax;

class SalesTaxPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return SalesTax::class;
    }
}
