<?php

namespace App\Http\RequestTransformers\Lead;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class LeadBulkDeleteTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'ids' => 'ids',
            ];
    }
}
