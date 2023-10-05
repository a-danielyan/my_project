<?php

namespace App\Http\RequestTransformers\Oauth2;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class Oauth2GetTransformer extends AbstractRequestTransformer
{
    /**
     * @return array
     */
    protected function getMap(): array
    {
        return array_merge_recursive(
            parent::paginationParams(),
            parent::sortingParams(),
            [
                'feature' => 'feature',
                'service' => 'service',
            ],
        );
    }
}
