<?php

namespace App\Http\Services\User;

use App\Http\Services\BaseLogService;
use App\Models\UserActivityLog;
use App\Models\UserLoginLog;
use Illuminate\Foundation\Auth\User;

/**
 * Class LogService
 */
class LogService extends BaseLogService
{
    /**
     * @return string
     */
    protected function logModel(): string
    {
        return UserActivityLog::class;
    }

    protected function logUserActivity(User $user, string $action, string $ip, ?User $impersonateUser = null): void
    {
        $data = [
            'user_id' => $user->getKey(),
            'status' => $action,
            'user_ip_address' => $ip,
            'impersonate_user_id' => $impersonateUser?->getKey(),
            'activity_time' => now(),
        ];

        UserLoginLog::query()->insert($data);
    }
}
