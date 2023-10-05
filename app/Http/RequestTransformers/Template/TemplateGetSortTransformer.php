<?php

namespace App\Http\RequestTransformers\Template;

use App\Http\RequestTransformers\BaseGetSortTransformer;

class TemplateGetSortTransformer extends BaseGetSortTransformer
{
    protected function getMap(): array
    {
        return
            [
                'entity' => 'entity',
                'template' => 'template',
                'isDefault' => 'is_default',
                'isShared' => 'is_shared',
                'tag' => 'tag',
                'name' => 'name',
                'status' => 'status',
                'thumbImage' => 'thumbImage',
                'group' => 'group',
            ];
    }
}
