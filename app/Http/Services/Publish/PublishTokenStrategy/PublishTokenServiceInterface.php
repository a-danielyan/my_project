<?php

namespace App\Http\Services\Publish\PublishTokenStrategy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

interface PublishTokenServiceInterface
{
    /**
     * @param array $params
     * @param User $user
     * @return Model
     */
    public function createToken(array $params, User $user): Model;

    /**
     * @param string $token
     * @return Model|null
     */
    public function getTokenDetails(string $token): ?Model;

    /**
     * @param string $token
     * @param string $entity
     * @return bool
     */
    public function isTokenCan(string $token, string $entity): bool;
}
