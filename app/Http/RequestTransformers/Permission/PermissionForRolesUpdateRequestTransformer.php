<?php

namespace App\Http\RequestTransformers\Permission;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class PermissionForRolesUpdateRequestTransformer extends AbstractRequestTransformer
{
    protected function getMap(): array
    {
        return [
            'permissionIds' => 'permissionIds',
        ];
    }
}
