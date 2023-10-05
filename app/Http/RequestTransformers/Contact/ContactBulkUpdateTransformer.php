<?php

namespace App\Http\RequestTransformers\Contact;

use App\Http\RequestTransformers\BaseBulkUpdateTransformer;

class ContactBulkUpdateTransformer extends BaseBulkUpdateTransformer
{
    protected function getMap(): array
    {
        return array_merge(
            parent::getMap(),
            [
                'salutation' => 'salutation',
                'accountId' => 'account_id',
            ],
        );
    }
}
