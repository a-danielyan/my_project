<?php

namespace App\Http\RequestTransformers\Sequence;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class SequenceStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'name' => 'name',
                'startDate' => 'start_date',
                'isActive' => 'is_active',
                'templates' => 'templates',
                'entity' => 'entity',
            ];
    }
}
