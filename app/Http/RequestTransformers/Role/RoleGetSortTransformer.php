<?php

namespace App\Http\RequestTransformers\Role;

use App\Http\RequestTransformers\BaseGetSortTransformer;

class RoleGetSortTransformer extends BaseGetSortTransformer
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
            ];
    }
}
