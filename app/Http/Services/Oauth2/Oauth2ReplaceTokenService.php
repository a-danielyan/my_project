<?php

namespace App\Http\Services\Oauth2;

use App\Exceptions\CustomErrorException;
use App\Models\OauthToken;
use Google\Service\Gmail;
use Illuminate\Foundation\Auth\User;

class Oauth2ReplaceTokenService
{
    /**
     * @param OauthToken $token
     * @param User $user
     * @param string $code
     * @return OauthToken
     * @throws CustomErrorException
     */
    public function replaceToken(OauthToken $token, User $user, string $code): OauthToken
    {
        $featureService = match ($token->service) {
            OauthToken::SERVICE_NAME_GOOGLE_MAIL => resolve(Gmail::class),
        };
        $oauth2Service = new Oauth2TokenService($featureService, $token->service);

        return $oauth2Service->getTokenByCode($code, $user);
    }
}
