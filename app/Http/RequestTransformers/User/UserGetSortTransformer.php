<?php

namespace App\Http\RequestTransformers\User;

use App\Http\RequestTransformers\BaseGetSortTransformer;

class UserGetSortTransformer extends BaseGetSortTransformer
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
                'phone' => 'phoneNo',
                'roleId' => 'role_id',
            ];
    }
}
