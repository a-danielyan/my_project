<?php

namespace App\Http\RequestTransformers\Account;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class AccountStoreTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return
            [
                'customFields' => 'customFields',
                'tag' => 'tag',
                'status' => 'status',
                'cmsClientId' => 'cms_client_id',
                'parentAccountId' => 'parent_account_id',
                'accountsPayable' => 'accountsPayable',
                'leadId' => 'lead_id',
                'avatar' => 'avatar',
                'avatarFile' => 'avatarFile',
                'internalNotes' => 'internalNotes',
            ];
    }
}
