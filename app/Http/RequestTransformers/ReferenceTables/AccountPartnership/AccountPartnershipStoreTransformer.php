<?php

namespace App\Http\RequestTransformers\ReferenceTables\AccountPartnership;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class AccountPartnershipStoreTransformer extends AbstractRequestTransformer
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
