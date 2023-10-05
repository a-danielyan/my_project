<?php

namespace App\Http\RequestTransformers\CustomField;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class CustomFieldStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'entityType' => 'entity_type',
                'name' => 'name',
                'type' => 'type',
                'lookupType' => 'lookup_type',
                'sortOrder' => 'sort_order',
                'isRequired' => 'is_required',
                'isMultiple' => 'is_multiple',
                'isUnique' => 'is_unique',
                'parentId' => 'parent_id',
                'status' => 'status',
            ];
    }
}
