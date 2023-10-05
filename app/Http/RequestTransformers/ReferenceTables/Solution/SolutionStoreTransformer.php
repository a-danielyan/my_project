<?php

namespace App\Http\RequestTransformers\ReferenceTables\Solution;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class SolutionStoreTransformer extends AbstractRequestTransformer
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
