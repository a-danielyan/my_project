<?php

namespace App\Http\RequestTransformers\PaymentProfile;

use App\Http\RequestTransformers\BaseGetSortTransformer;

class PaymentProfileGetSortTransformer extends BaseGetSortTransformer
{
    protected function getMap(): array
    {
        return
            [
                'accountId' => 'account_id',
            ];
    }
}
