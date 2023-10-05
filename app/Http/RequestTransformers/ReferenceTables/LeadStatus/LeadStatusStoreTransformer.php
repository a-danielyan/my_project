<?php

namespace App\Http\RequestTransformers\ReferenceTables\LeadStatus;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class LeadStatusStoreTransformer extends AbstractRequestTransformer
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
