<?php

namespace App\Http\RequestTransformers\Contact;

use App\Http\RequestTransformers\BaseGetSortTransformer;

class ContactGetSortTransformer extends BaseGetSortTransformer
{
    /**
     * To map fields
     *
     * @return array
     */
    protected function getMap(): array
    {
        return
            [
                'status' => 'status',
                'accountId' => 'account_id',
            ];
    }
}
