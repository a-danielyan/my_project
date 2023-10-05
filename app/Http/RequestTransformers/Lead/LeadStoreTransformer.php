<?php

namespace App\Http\RequestTransformers\Lead;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class LeadStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'salutation' => 'salutation',
                'customFields' => 'customFields',
                'tag' => 'tag',
                'status' => 'status',
                'avatar' => 'avatar',
                'avatarFile' => 'avatarFile',
                'internalNotes' => 'internalNotes',
            ];
    }
}
