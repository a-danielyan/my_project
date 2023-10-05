<?php

namespace App\Http\RequestTransformers\Role;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class RoleTransformer extends AbstractRequestTransformer
{
    /**
     * @return array
     */
    protected function getMap(): array
    {
        return [
            'name' => 'name',
            'description' => 'description',
            'status' => 'status',
        ];
    }
}
