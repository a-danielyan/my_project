<?php

namespace App\Http\RequestTransformers\Contact;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class ContactStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'salutation' => 'salutation',
                'customFields' => 'customFields',
                'accountId' => 'account_id',
                'tag' => 'tag',
                'status' => 'status',
                'avatar' => 'avatar',
                'avatarFile' => 'avatarFile',
            ];
    }
}
