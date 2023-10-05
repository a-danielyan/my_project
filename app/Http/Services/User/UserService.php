<?php

namespace App\Http\Services\User;

use App\Http\Repositories\UserRepository;
use App\Http\Resource\UserResource;
use App\Http\Services\BaseService;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class UserService
 */
class UserService extends BaseService
{
    /**
     * UserService constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository,
    ) {
        $this->repository = $userRepository;
    }

    /**
     * @return string
     */
    public function resource(): string
    {
        return UserResource::class;
    }

    /**
     * @param array $params
     * @param User $user
     * @return array
     */
    public function getAll(array $params, User|Authenticatable $user): array
    {
        return $this->paginate($this->repository->getAll($params, $user));
    }
}
