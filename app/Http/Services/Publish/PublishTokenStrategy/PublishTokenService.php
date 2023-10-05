<?php

namespace App\Http\Services\Publish\PublishTokenStrategy;

use App\Http\Repositories\PublishRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;

class PublishTokenService implements PublishTokenServiceInterface
{
    protected PublishRepository $publishRepository;

    /**
     * @param PublishRepository $publishRepository
     */
    public function __construct(PublishRepository $publishRepository)
    {
        $this->publishRepository = $publishRepository;
    }

    /**
     * @param array $params
     * @param User $user
     * @return Model
     */
    public function createToken(array $params, User $user): Model
    {
        return $this->publishRepository->create(
            [
                'token' => hash('sha256', Str::random(40)),
                'entity_type' => $params['entity_type'],
                'entity_id' => $params['entity_id'],
                'created_by' => $user->getKey(),
            ]
        );
    }

    /**
     * @param string $token
     * @return Model|null
     */
    public function getTokenDetails(string $token): ?Model
    {
        return $this->publishRepository->findToken($token);
    }


    /**
     * @param string|null $token
     * @param string|null $entity
     * @return bool
     */
    public function isTokenCan(?string $token, ?string $entity): bool
    {
        if (!$token) {
            return false;
        }

        $accessToken = $this->publishRepository->getValidToken($token, $entity);

        if (!$accessToken) {
            return false;
        }

        return true;
    }
}
