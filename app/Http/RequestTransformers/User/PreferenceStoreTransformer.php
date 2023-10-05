<?php

namespace App\Http\RequestTransformers\User;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class PreferenceStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return [
            'name' => 'name',
            'settings' => 'settings',
            'entity' => 'entity',
        ];
    }
}
