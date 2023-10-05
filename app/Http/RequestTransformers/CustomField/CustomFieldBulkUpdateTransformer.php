<?php

namespace App\Http\RequestTransformers\CustomField;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class CustomFieldBulkUpdateTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'entityType' => 'entity_type',
                'fields' => 'fields',
            ];
    }
}
