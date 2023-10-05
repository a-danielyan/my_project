<?php

namespace App\Http\Services\Oauth2;

use App\DTO\OAuth2DTO;
use App\Exceptions\CustomErrorException;

interface Oauth2Interface
{
    /**
     * @throws CustomErrorException
     */
    public function getAccessToken(string $code): OAuth2DTO;
    public function refreshAccessToken(string $refreshToken, array $options = []): OAuth2DTO;
//    public function setAccessToken(string $token): void;
}
