<?php

namespace App\Policies;

use App\Models\User as UserModel;
use Illuminate\Foundation\Auth\User;

class PermissionPolicy extends BasePolicy
{
    public function viewAny(User $initiator): bool
    {
        /** @var UserModel $initiator */
        return true;
    }

    public function updatePermission(User $initiator): bool
    {
        /** @var UserModel $initiator */
        return true;
    }
}
