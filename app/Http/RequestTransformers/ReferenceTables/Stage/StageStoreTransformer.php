<?php

namespace App\Http\RequestTransformers\ReferenceTables\Stage;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class StageStoreTransformer extends AbstractRequestTransformer
{
    /**
     * @return array
     */
    protected function getMap(): array
    {
        return [
            'name' => 'name',
            'status' => 'status',
            'sortOrder' => 'sort_order',
        ];
    }
}
