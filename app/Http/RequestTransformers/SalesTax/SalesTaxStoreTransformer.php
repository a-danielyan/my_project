<?php

namespace App\Http\RequestTransformers\SalesTax;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class SalesTaxStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'stateCode' => 'state_code',
                'tax' => 'tax',
            ];
    }
}
