<?php

namespace App\Http\RequestTransformers\ReferenceTables\ContactType;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class ContactTypeStoreTransformer extends AbstractRequestTransformer
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
