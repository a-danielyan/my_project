<?php

namespace App\Http\RequestTransformers\Auth;

use App\Http\RequestTransformers\AbstractRequestTransformer;

/**
 * Class AuthUpdateTransformer
 * @package App\Http\RequestTransformers\Common\Auth
 */
class AuthUpdateTransformer extends AbstractRequestTransformer
{
    /**
     * To map fields
     *
     * @return array
     */
    protected function getMap(): array
    {
        return [
            'firstName' => 'first_name',
            'lastName' => 'last_name',
            'email' => 'email',
            'phone' => 'phone_no',
            'themeMode' => 'theme_mode',
            'profile' => 'profile',
            'userSignature' => 'user_signature',
            'dashboardBlocks' => 'dashboard_blocks',
        ];
    }
}
