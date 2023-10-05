<?php

namespace App\Http\Repositories;

use App\Models\User;
use App\Models\UserLoginLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class UserRepository
 */
class UserRepository extends BaseRepository
{
    private UserLoginLog $logModel;
    /**
     * @param User $systemUser
     * @param UserLoginLog $loginLog
     */
    public function __construct(User $systemUser, UserLoginLog $loginLog)
    {
        $this->model = $systemUser;
        $this->logModel = $loginLog;
    }

    /**
     * @param array $params
     * @param User $user
     * @return LengthAwarePaginator
     */
    public function getAll(array $params, User $user): LengthAwarePaginator
    {
        return $this->prepareQueryForGet($params, $user)
            ->with(
                'storedFiles',
                'role',
                'role.permissions',
                'role.permissions.customField',
                'userDataFile',
                'lastLogin',
                'lastAuthActivity',
            )
            ->paginate($params['limit']);
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findActiveUserByEmail(string $email): ?User
    {
        return User::query()->where(
            [
                'email' => $email,
                'deleted_at' => null,
            ],
        )->first();
    }
}
