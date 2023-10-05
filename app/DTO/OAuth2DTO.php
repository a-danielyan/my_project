<?php

namespace App\DTO;

use App\Exceptions\CustomErrorException;
use Illuminate\Support\Carbon;

class OAuth2DTO
{
    public string $accessToken;
    public ?string $refreshToken;
    public ?Carbon $expireOn;
    public ?array $attributes;
    public ?string $userName;
    public ?int $id;

    /**
     * @param array $data
     * @return static
     * @throws CustomErrorException
     */
    public static function fromArray(array $data): static
    {
        $oauth2 = new static();
        $data = new DataSet($data);
        $oauth2->accessToken = $data->get('access_token');
        $oauth2->refreshToken = $data->get('refresh_token', false);
        $oauth2->expireOn = $data->get('expire_on', false)
            ? new Carbon($data->get('expire_on', false)) : null;
        $oauth2->attributes = is_array($data->get('attributes', false))
            ? $data->get('attributes')
            : [];
        $oauth2->userName = $data->get('userName', false);

        return $oauth2;
    }
}
