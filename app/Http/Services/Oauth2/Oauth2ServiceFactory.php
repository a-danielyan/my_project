<?php

namespace App\Http\Services\Oauth2;

use App\Http\Services\GmailService;
use App\Models\OauthToken;
use Illuminate\Foundation\Auth\User;

class Oauth2ServiceFactory
{
    public static function createService(string $serviceName, ?User $user): Oauth2Interface
    {
        return match ($serviceName) {
            OauthToken::SERVICE_NAME_GOOGLE_MAIL => resolve(GmailService::class),
        };
    }

    public static function createServiceFromToken(OauthToken $oauthToken, ?User $user): Oauth2Interface
    {
        $oauth2Service = self::createService($oauthToken->service, $user);

        (new Oauth2TokenService(
            $oauth2Service,
            $oauthToken->service
        ))->refreshToken($oauthToken);

        $oauth2Service->setAccessToken($oauthToken->access_token);

        return $oauth2Service;
    }
}
