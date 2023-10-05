<?php

namespace App\Http\RequestTransformers\ReferenceTables\LeadType;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class LeadTypeStoreTransformer extends AbstractRequestTransformer
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
