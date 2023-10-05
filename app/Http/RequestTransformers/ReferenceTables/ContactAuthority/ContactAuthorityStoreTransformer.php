<?php

namespace App\Http\RequestTransformers\ReferenceTables\ContactAuthority;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class ContactAuthorityStoreTransformer extends AbstractRequestTransformer
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
