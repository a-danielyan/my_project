<?php

namespace App\Http\Resource;

use App\Models\OauthToken;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin OauthToken
 */
class Oauth2Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'service' => $this->service,
            'userName' => $this->user_name,
        ];
    }
}
