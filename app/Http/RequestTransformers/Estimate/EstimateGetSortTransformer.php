<?php

namespace App\Http\RequestTransformers\Estimate;

use App\Http\RequestTransformers\BaseGetSortTransformer;

class EstimateGetSortTransformer extends BaseGetSortTransformer
{
    protected function getMap(): array
    {
        return
            [
                'estimateName' => 'estimate_name',
                'estimateValidityDuration' => 'estimate_validity_duration',
            ];
    }
}
