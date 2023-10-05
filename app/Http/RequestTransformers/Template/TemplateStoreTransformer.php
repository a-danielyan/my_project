<?php

namespace App\Http\RequestTransformers\Template;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class TemplateStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'entity' => 'entity',
                'template' => 'template',
                'isDefault' => 'is_default',
                'tag' => 'tag',
                'name' => 'name',
                'status' => 'status',
                'thumbImage' => 'thumbImage',
            ];
    }
}
