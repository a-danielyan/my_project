<?php

namespace App\Http\Services;

use App\Models\Interfaces\LoggableModelInterface;
use App\Models\User;

/**
 * Class BaseLogService
 * @package App\Http\Services
 */
abstract class BaseLogService
{
    // Actions with models
    private const NEW_ACTION = 'New';

    private const UPDATE_ACTION = 'Update';

    private const DELETE_ACTION = 'Delete';

    // Authentication actions
    private const LOGIN_ACTION = 'Login';

    private const LOGOUT_ACTION = 'Logout';

    /**
     * @return string
     */
    abstract protected function logModel(): string;

    /**
     * Log
     *
     * @param int $componentId
     * @param string $componentName
     * @param string $activity
     * @param int $userId
     * @return void
     */
    private function log(int $componentId, string $componentName, string $activity, int $userId): void
    {
        $this->logModel()::saveActivityData($componentId, $componentName, $activity, $userId);
    }


    abstract protected function logUserActivity(
        User $user,
        string $action,
        string $ip,
        ?User $impersonateUser = null,
    );

    /**
     * Insert Log
     *
     * @param LoggableModelInterface $model
     * @param User $user
     * @return void
     */
    public function insertLog(LoggableModelInterface $model, User $user): void
    {
        $this->log($model->getKey(), $model::getLoggableName(), self::NEW_ACTION, $user->getKey());
    }

    /**
     * Update Log
     *
     * @param LoggableModelInterface $model
     * @param User $user
     * @return void
     */
    public function updateLog(LoggableModelInterface $model, User $user): void
    {
        $this->log($model->getKey(), $model::getLoggableName(), self::UPDATE_ACTION, $user->getKey());
    }

    /**
     * Delete Log
     *
     * @param LoggableModelInterface $model
     * @param User $user
     * @return void
     */
    public function deleteLog(LoggableModelInterface $model, User $user): void
    {
        $this->log($model->getKey(), $model->getLoggableName(), self::DELETE_ACTION, $user->getKey());
    }

    /**
     * Login log
     *
     * @param string $ip
     * @param User $user
     * @param User|null $impersonateUser
     * @return void
     */
    public function loginLog(string $ip, User $user, User $impersonateUser = null): void
    {
        $this->logUserActivity($user, self::LOGIN_ACTION, $ip, $impersonateUser);
    }

    /**
     * Logout log
     *
     * @param string $ip
     * @param User $user
     * @return void
     */
    public function logoutLog(string $ip, User $user): void
    {
        $this->logUserActivity($user, self::LOGOUT_ACTION, $ip);
    }
}
