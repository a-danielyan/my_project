<?php

namespace App\Http\Repositories;

use App\Models\SalesTax;

class SalesTaxRepository extends BaseRepository
{
    /**
     * @param SalesTax $salesTax
     */
    public function __construct(SalesTax $salesTax)
    {
        $this->model = $salesTax;
    }
}
