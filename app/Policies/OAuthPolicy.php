<?php

namespace App\Policies;

use App\Models\OauthToken;

class OAuthPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return OauthToken::class;
    }
}
