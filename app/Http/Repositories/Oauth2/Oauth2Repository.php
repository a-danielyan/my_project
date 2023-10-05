<?php

namespace App\Http\Repositories\Oauth2;

use App\DTO\OAuth2DTO;
use App\Http\Repositories\BaseRepository;
use App\Models\OauthToken;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;

class Oauth2Repository extends BaseRepository
{
    /**
     * @param OauthToken $oauthToken
     */
    public function __construct(OauthToken $oauthToken)
    {
        $this->model = $oauthToken;
    }

    public function getTokenById(int $tokenId): OauthToken|Model
    {
        $where = [
            'id' => $tokenId,
            'user_id' => 1,
        ];

        return $this->firstOrFail(null, where: $where);
    }

    public function getToken(
        string $serviceName,
        int $userId,
        ?int $identifier = null,
        ?string $userName = null,
    ): OauthToken|Model|null {
        $where = [
            'service' => $serviceName,
            'user_id' => $userId,
        ];
        if ($identifier) {
            $where['id'] = $identifier;
        }
        if ($userName) {
            $where['user_name'] = $userName;
        }

        return $this->first([], [], $where);
    }


    public function updateToken(OauthToken $token, OAuth2DTO $data): OauthToken
    {
        $token->access_token = $data->accessToken;
        $token->refresh_token = $data->refreshToken;
        $token->expire_on = $data->expireOn;
        $token->save();

        return $token;
    }

    /**
     * @param array $exclude
     * @return Collection
     */
    public function getExpiredTokens(array $exclude = []): Collection
    {
        if (empty($exclude)) {
            return $this->model
                ->with('feature')
                ->where('expire_on', '<=', Carbon::now()->addMinutes(5))
                ->get();
        }

        return $this->model
            ->with('feature')
            ->where('expire_on', '<=', Carbon::now()->addMinutes(5))
            ->whereNotIn('service_id', $exclude)
            ->get();
    }


    public function getTokens(User $user, string $service): Collection
    {
        $where = [
            'service' => $service,
            'user_id' => $user->getKey(),
        ];

        return $this->get(where: $where);
    }
}
