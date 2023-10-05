<?php

namespace App\Http\RequestTransformers\Product;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class ProductStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'customFields' => 'customFields',
                'tag' => 'tag',
                'status' => 'status',
            ];
    }
}
