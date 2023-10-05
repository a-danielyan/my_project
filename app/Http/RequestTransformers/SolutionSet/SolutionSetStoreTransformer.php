<?php

namespace App\Http\RequestTransformers\SolutionSet;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class SolutionSetStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'name' => 'name',
                'items' => 'items',
            ];
    }
}
