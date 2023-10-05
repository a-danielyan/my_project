<?php

namespace App\Http\RequestTransformers\Email;

use App\Http\RequestTransformers\BaseGetSortTransformer;

class EmailGetSortTransformer extends BaseGetSortTransformer
{
    protected function getMap(): array
    {
        return array_merge(
            parent::getMap(),
            [
                'relatedToEntity' => 'relatedToEntity',
                'relatedToId' => 'relatedToId',
            ],
        );
    }
}
