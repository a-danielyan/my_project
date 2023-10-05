<?php

namespace App\Http\RequestTransformers\ReferenceTables\LeadSource;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class LeadSourceStoreTransformer extends AbstractRequestTransformer
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
