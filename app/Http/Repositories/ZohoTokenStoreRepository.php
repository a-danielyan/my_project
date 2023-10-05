<?php

namespace App\Http\Repositories;

use App\Models\OauthToken;
use Carbon\Carbon;
use com\zoho\api\authenticator\OAuthBuilder;
use com\zoho\api\authenticator\OAuthToken as ZohoOauthToken;
use com\zoho\api\authenticator\store\TokenStore;
use com\zoho\api\authenticator\Token;
use com\zoho\crm\api\exception\SDKException;
use com\zoho\crm\api\UserSignature;
use com\zoho\crm\api\util\Constants;
use Exception;
use ReflectionClass;

class ZohoTokenStoreRepository implements TokenStore
{
    /**
     * @param Token $token
     * @return ZohoOauthToken|Token|null
     * @throws SDKException
     */
    public function findToken(Token $token): ZohoOauthToken|Token|null
    {
        try {
            $oauthToken = $token;
            $query = OauthToken::query()->where('service', 'zohocrm');

            if ($oauthToken->getUserSignature() != null) {
                $name = $oauthToken->getUserSignature()->getName();
                if ($name != null && strlen($name) > 0) {
                    $query = $query->where('user_name', $name);
                }
            } elseif (
                $oauthToken->getAccessToken() != null &&
                $oauthToken->getClientId() == null &&
                $oauthToken->getClientSecret() == null
            ) {
                $query = $query->where('access_token', $oauthToken->getAccessToken());
            } elseif (
                ($oauthToken->getRefreshToken() != null || $oauthToken->getGrantToken() != null) &&
                $oauthToken->getClientId() != null &&
                $oauthToken->getClientSecret() != null
            ) {
                if ($oauthToken->getGrantToken() != null && strlen($oauthToken->getGrantToken()) > 0) {
                    $query = $query->where('grant_token', $oauthToken->getGrantToken());
                } elseif ($oauthToken->getRefreshToken() != null && strlen($oauthToken->getRefreshToken()) > 0) {
                    $query = $query->where('refresh_token', $oauthToken->getRefreshToken());
                }
            }

            try {
                /** @var OauthToken $result */
                $result = $query->first();
                if (!$result) {
                    return null;
                }

                $this->setMergeData($oauthToken, $result);
            } catch (Exception $e) {
                throw new SDKException(Constants::TOKEN_STORE, Constants::GET_TOKEN_DB_ERROR1 . $e);
            }
        } catch (Exception $ex) {
            throw new SDKException(Constants::TOKEN_STORE, Constants::GET_TOKEN_DB_ERROR1 . $ex);
        }

        return $token;
    }

    /**
     * @param $id
     * @return ZohoOauthToken|Token
     * @throws SDKException
     */
    public function findTokenById($id): Token|ZohoOauthToken
    {
        try {
            $class = new ReflectionClass(ZohoOauthToken::class);

            $oauthToken = $class->newInstanceWithoutConstructor();

            /** @var OauthToken $result */
            $result = OauthToken::query()->where('service', 'zohocrm')->where('id', $id)->first();

            if (!$result) {
                throw new SDKException(Constants::TOKEN_STORE, Constants::GET_TOKEN_BY_ID_DB_ERROR);
            }

            $this->setMergeData($oauthToken, $result);

            return $oauthToken;
        } catch (SDKException $ex) {
            throw $ex;
        } catch (Exception $ex) {
            throw new SDKException(Constants::TOKEN_STORE, Constants::GET_TOKEN_BY_ID_DB_ERROR, $ex);
        }
    }

    /**
     * @param Token $token
     * @return void
     * @throws SDKException
     */
    public function saveToken(Token $token): void
    {
        $query = OauthToken::query()->where('service', 'zohocrm');

        if ($token->getUserSignature() != null) {
            $name = $token->getUserSignature()->getName();
            if ($name != null && strlen($name) > 0) {
                $query = $query->where('user_name', $name);
            }
        } elseif (
            $token->getAccessToken() != null &&
            strlen($token->getAccessToken()) > 0 &&
            $token->getClientId() == null &&
            $token->getClientSecret() == null
        ) {
            $query = $query->where('access_token', $token->getAccessToken());
        } elseif (
            (
                ($token->getRefreshToken() != null && strlen($token->getRefreshToken()) > 0) ||
                ($token->getGrantToken() != null && strlen($token->getGrantToken()) > 0)) &&
            $token->getClientId() != null &&
            $token->getClientSecret() != null
        ) {
            if ($token->getGrantToken() != null && strlen($token->getGrantToken()) > 0) {
                $query = $query->where('grant_token', $token->getGrantToken());
            } else {
                if ($token->getRefreshToken() != null && strlen($token->getRefreshToken()) > 0) {
                    $query = $query->where('refresh_token', $token->getRefreshToken());
                }
            }
        }

        try {
            /** @var OauthToken $savedToken */
            $savedToken = $query->first();

            if (!$savedToken) {
                if ($token->getId() != null || $token->getUserSignature() != null) {
                    if (
                        $token->getRefreshToken() == null &&
                        $token->getGrantToken() == null &&
                        $token->getAccessToken() == null
                    ) {
                        throw new SDKException(Constants::TOKEN_STORE, Constants::GET_TOKEN_DB_ERROR1);
                    }
                }

                $user_name = $token->getUserSignature()?->getName();
                OauthToken::query()->create([
                    'user_id' => 1,
                    'access_token' => $token->getAccessToken(),
                    'grant_token' => $token->getGrantToken(),
                    'refresh_token' => $token->getRefreshToken(),
                    'expire_on' => Carbon::createFromTimestamp($token->getExpiresIn() / 1000),
                    'user_name' => $user_name,
                    'client_id' => $token->getClientId(),
                    'client_secret' => $token->getClientSecret(),
                    'expires_in' => $token->getExpiresIn(),
                    'redirect_url' => $token->getRedirectURL(),
                ]);
            } else {
                if ($token->getUserSignature() != null) {
                    $user_name = $token->getUserSignature()->getName();
                    $savedToken->user_name = $user_name;
                }

                $savedToken->access_token = $token->getAccessToken();
                $savedToken->grant_token = $token->getGrantToken();
                $savedToken->refresh_token = $token->getRefreshToken();
                $savedToken->expire_on = Carbon::createFromTimestamp($token->getExpiresIn() / 1000);
                $savedToken->client_id = $token->getClientId();
                $savedToken->client_secret = $token->getClientSecret();
                $savedToken->expires_in = $token->getExpiresIn();
                $savedToken->redirect_url = $token->getRedirectURL();
                $savedToken->save();
            }
        } catch (Exception $e) {
            throw new SDKException(Constants::TOKEN_STORE, Constants::SAVE_TOKEN_DB_ERROR, $e);
        }
    }

    public function deleteToken($id): void
    {
        OauthToken::query()->where('service', 'zohocrm')->where('id', $id)->delete();
    }

    /**
     * @return array
     * @throws SDKException
     */
    public function getTokens(): array
    {
        $tokens = [];

        try {
            $result = OauthToken::query()->where('service', 'zohocrm')->get();

            if ($result) {
                foreach ($result as $row) {
                    /** @var OauthToken $row */
                    $grantToken = $row->grant_token;

                    $token = (new OAuthBuilder())->clientId($row->client_id)
                        ->clientSecret($row->client_secret)
                        ->refreshToken($row->refresh_token)->build();

                    $token->setId($row->id);

                    if ($grantToken != null) {
                        $token->setGrantToken($grantToken);
                    }
                    $name = $row->user_name;
                    if ($name != null) {
                        $token->setUserSignature(new UserSignature($name));
                    }

                    $token->setAccessToken($row->access_token);
                    $token->setExpiresIn($row->expires_in);
                    $token->setRedirectURL($row->redirect_url);

                    $tokens[] = $token;
                }
            }
        } catch (Exception $ex) {
            throw new SDKException(Constants::TOKEN_STORE, Constants::GET_TOKENS_DB_ERROR, null, $ex);
        }

        return $tokens;
    }

    public function deleteTokens(): void
    {
        OauthToken::query()->where('service', 'zohocrm')->delete();
    }

    /**
     * @param ZohoOauthToken $oauthToken
     * @param OauthToken $result
     * @return void
     * @throws SDKException
     */
    private function setMergeData(ZohoOauthToken $oauthToken, OauthToken $result): void
    {
        if ($oauthToken->getId() == null) {
            $oauthToken->setId($result->id);
        }
        if ($oauthToken->getUserSignature() == null) {
            $name = $result->user_name;
            if ($name != null) {
                $oauthToken->setUserSignature(new UserSignature($name));
            }
        }
        if ($oauthToken->getClientId() == null) {
            $oauthToken->setClientId($result->client_id);
        }
        if ($oauthToken->getClientSecret() == null) {
            $oauthToken->setClientSecret($result->client_secret);
        }
        if ($oauthToken->getRefreshToken() == null) {
            $oauthToken->setRefreshToken($result->refresh_token);
        }
        if ($oauthToken->getAccessToken() == null) {
            $oauthToken->setAccessToken($result->access_token);
        }
        if ($oauthToken->getGrantToken() == null) {
            $oauthToken->setGrantToken($result->grant_token);
        }
        if ($oauthToken->getExpiresIn() == null) {
            $expiresIn = $result->expires_in;
            if ($expiresIn != null) {
                $oauthToken->setExpiresIn($expiresIn);
            }
        }
        if ($oauthToken->getRedirectURL() == null) {
            $oauthToken->setRedirectURL($result->redirect_url);
        }
    }
}
