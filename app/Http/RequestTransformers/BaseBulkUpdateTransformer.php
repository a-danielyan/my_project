<?php

namespace App\Http\RequestTransformers;

class BaseBulkUpdateTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'ids' => 'ids',
                'customFields' => 'customFields',
                'tag' => 'tag',
                'status' => 'status',
            ];
    }
}
