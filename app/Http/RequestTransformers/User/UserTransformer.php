<?php

namespace App\Http\RequestTransformers\User;

use App\Http\RequestTransformers\AbstractRequestTransformer;

class UserTransformer extends AbstractRequestTransformer
{
    /**
     * @return array
     */
    protected function getMap(): array
    {
        return [
            'firstName' => 'first_name',
            'lastName' => 'last_name',
            'email' => 'email',
            'password' => 'password',
            'phone' => 'phone_no',
            'roleId' => 'role_id',
            'profile' => 'profile',
            'status' => 'status',
            'dashboardBlocks' => 'dashboard_blocks',
        ];
    }
}
