<?php

namespace App\Http\RequestTransformers\ReferenceTables\Industry;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class IndustryStoreTransformer extends AbstractRequestTransformer
{
    /**
     * @return array
     */
    protected function getMap(): array
    {
        return [
            'name' => 'name',
            'status' => 'status',
        ];
    }
}
