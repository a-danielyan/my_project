<?php

namespace App\Http\Services\Oauth2;

use App\DTO\OAuth2DTO;
use App\Exceptions\CustomErrorException;
use Google\Client as GoogleClient;
use Google\Service\Gmail;
use Illuminate\Support\Carbon;
use Throwable;

abstract class GoogleOauth2ServiceBase implements Oauth2Interface
{
    protected const GOOGLE_API_ACCESS_TYPE = 'offline';

    protected GoogleClient $googleClient;

    /**
     * @return array ['client_id' => (string), 'client_secret' => (string)]
     */
    abstract protected function getCredentials(): array;

    abstract protected function getScopes(): array;

    abstract protected function getOauth2RedirectUrl(): string;

    public function __construct(GoogleClient $googleClient)
    {
        $credentials = $this->getCredentials();
        $this->googleClient = $googleClient;
        $this->googleClient->setClientId($credentials['client_id']);
        $this->googleClient->setClientSecret($credentials['client_secret']);
        $this->googleClient->setRedirectUri($this->getOauth2RedirectUrl());
        $this->googleClient->setAccessType(static::GOOGLE_API_ACCESS_TYPE);
        $this->googleClient->setScopes($this->getScopes());
    }

    /**
     * @param string $code
     * @return OAuth2DTO
     * @throws CustomErrorException
     */
    public function getAccessToken(string $code): OAuth2DTO
    {
        $res = $this->googleClient->fetchAccessTokenWithAuthCode($code);

        $this->checkError($res);

        $dto = OAuth2DTO::fromArray($res);
        $dto->expireOn = Carbon::now()->addSeconds($res['expires_in']);
        $this->setAccessToken($dto->accessToken);
        try {
            $this->getUserEmail($dto);
        } catch (Throwable) {
        }

        return $dto;
    }

    /**
     * @param string $refreshToken
     * @param array $options
     * @return OAuth2DTO
     * @throws CustomErrorException
     */
    public function refreshAccessToken(string $refreshToken, array $options = []): OAuth2DTO
    {
        $res = $this->googleClient->fetchAccessTokenWithRefreshToken($refreshToken);

        $this->checkError($res);

        $dto = OAuth2DTO::fromArray($res);
        $dto->expireOn = Carbon::now()->addSeconds($res['expires_in']);
        $this->setAccessToken($dto->accessToken);

        return $dto;
    }

    public function setAccessToken(string $token): void
    {
        $this->googleClient->setAccessToken($token);
    }

    /**
     * @param array|null $res
     * @return void
     * @throws CustomErrorException
     */
    private function checkError(?array $res): void
    {
        if (isset($res['error'])) {
            throw new CustomErrorException($res['error_description'] ?? $res['error'], 422);
        }

        if (empty($res)) {
            throw new CustomErrorException('No response from API');
        }
    }

    /**
     * @param OAuth2DTO $dto
     * @return void
     */
    protected function getUserEmail(OAuth2DTO $dto): void
    {
        $gmail = new Gmail($this->googleClient);
        $userEmailAccount = $gmail->users->getProfile('me');
        $dto->userName = $userEmailAccount->getEmailAddress();
    }
}
