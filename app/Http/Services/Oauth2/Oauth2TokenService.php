<?php

namespace App\Http\Services\Oauth2;

use App\DTO\OAuth2DTO;
use App\Exceptions\CustomErrorException;
use App\Exceptions\Oauth2TokenNotGeneratedException;
use App\Http\Repositories\Oauth2\Oauth2Repository;
use App\Models\OauthToken;
use Illuminate\Foundation\Auth\User;

class Oauth2TokenService
{
    public const ERROR_INVALID_AUTH_CODE = 'Authentication code is invalid';

    protected Oauth2Interface $oauth2Service;
    protected string $serviceName;

    protected Oauth2Repository $repository;

    public function __construct(
        Oauth2Interface $oauth2Service,
        string $serviceName,
    ) {
        $this->oauth2Service = $oauth2Service;
        $this->serviceName = $serviceName;
        $this->repository = resolve(Oauth2Repository::class);
    }

    /**
     * @param User $user
     * @param int|null $identifier
     * @return OauthToken|null
     * @throws Oauth2TokenNotGeneratedException
     */
    public function getToken(User $user, ?int $identifier = null): ?OauthToken
    {
        $token = $this->repository->getToken(
            $this->serviceName,
            $user->getKey(),
        );

        if (!$token) {
            throw new Oauth2TokenNotGeneratedException();
        }

        $this->refreshToken($token);

        return $token;
    }

    public function refreshToken(OauthToken $token): void
    {
        if ($token->is_expired && $token->refresh_token) {
            $this->repository->updateToken(
                $token,
                $this->oauth2Service->refreshAccessToken($token->refresh_token, $token->attributes ?? []),
            );
        }
    }

    /**
     * @param string $code
     * @param User $user
     * @return OauthToken
     * @throws CustomErrorException
     */
    public function getTokenByCode(string $code, User $user): OauthToken
    {
        try {
            $dto = $this->oauth2Service->getAccessToken($code);
        } catch (CustomErrorException $e) {
            throw new CustomErrorException(self::ERROR_INVALID_AUTH_CODE . ' ' . $e->getMessage(), 400);
        }

        return $this->saveToken($dto, $user);
    }

    private function saveToken(OAuth2DTO $dto, User $user): OauthToken
    {
        $oauthToken = $this->repository->getToken(
            $this->serviceName,
            $user->getKey(),
            userName: $dto->userName,
        );

        if (!$oauthToken) {
            $oauthToken = new OauthToken();
            $oauthToken->service = $this->serviceName;
        }

        if (!empty($dto->attributes)) {
            $oauthToken->attributes = $dto->attributes;
        }

        if (!empty($dto->userName)) {
            $oauthToken->user_name = $dto->userName;
        }

        $oauthToken->access_token = $dto->accessToken;
        $oauthToken->refresh_token = $dto->refreshToken;
        $oauthToken->expire_on = $dto->expireOn;
        $oauthToken->user_id = $user->getKey();
        $oauthToken->save();

        return $oauthToken;
    }
}
