<?php

namespace App\Http\Services;

use App\Exceptions\ZohoAPILimitException;
use App\Helpers\zohoCache\ZohoCacheItemPool;
use App\Models\OauthToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Weble\ZohoClient\Enums\Region;
use Weble\ZohoClient\Exception\AccessDeniedException;
use Weble\ZohoClient\Exception\AccessTokenNotSet;
use Weble\ZohoClient\Exception\RefreshTokenNotSet;
use Weble\ZohoClient\OAuthClient;
use Webleit\ZohoBooksApi\Client;

class ZohoBooksService
{
    private Client $client;
    public const MINIMUM_REMAINING_API_REQUESTS = 2000;
    private const ZOHO_RATE_LIMITER_KEY = 'zoho-books-api';
    private const  RATE_LIMITER_THREASHOLD = 20;

    /**
     * @param OauthToken $token
     * @return Client
     * @throws IdentityProviderException
     * @throws AccessDeniedException
     * @throws AccessTokenNotSet
     * @throws RefreshTokenNotSet
     */
    public function initializeZoho(OauthToken $token): Client
    {
        $cache = new ZohoCacheItemPool();
        // set up the generic zoho oath client
        $oAuthClient = new OAuthClient($token->client_id, $token->client_secret);
        $oAuthClient->setRefreshToken($token->refresh_token);
        $oAuthClient->setRegion(Region::US);
        $oAuthClient->useCache($cache);
        $oAuthClient->offlineMode();

// Access Token
        $accessToken = $oAuthClient->getAccessToken();
        $token->access_token = $accessToken;
        $token->save();

// set up the zoho books client
        $this->client = new Client($oAuthClient);
        $this->client->setOrganizationId(config('services.zohoCrm.zohoBooksOrganizationId'));

        return $this->client;
    }

    /**
     * @return void
     * @throws ZohoAPILimitException
     */
    public function validateRequestUsages(): void
    {
        if (RateLimiter::tooManyAttempts(self::ZOHO_RATE_LIMITER_KEY, self::RATE_LIMITER_THREASHOLD)) {
            $seconds = RateLimiter::availableIn(self::ZOHO_RATE_LIMITER_KEY);
            sleep($seconds + 1);
        }

        $executed = RateLimiter::attempt(
            self::ZOHO_RATE_LIMITER_KEY,
            self::RATE_LIMITER_THREASHOLD,
            function () {
                $limitRemaining = $this->client->getRateLimitRemaining();

                if ($limitRemaining !== null && $limitRemaining < self::MINIMUM_REMAINING_API_REQUESTS) {
                    Log::error('Zoho books daily limit reached. We left only ' . $limitRemaining . ' API calls');
                    throw new ZohoAPILimitException('Daily API Limits reached', 422);
                }
            },
        );

        if (!$executed) {
            throw new ZohoAPILimitException('Daily API Limits reached', 422);
        }
    }
}
