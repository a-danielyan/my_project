<?php

namespace App\Http\RequestTransformers\Tag;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class TagStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'tag' => 'tag',
                'backgroundColor' => 'background_color',
                'textColor' => 'text_color',
                'status' => 'status',
                'entityType' => 'entity_type',
            ];
    }
}
