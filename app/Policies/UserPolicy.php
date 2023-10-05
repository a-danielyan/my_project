<?php

namespace App\Policies;

use App\Models\User as UserModel;

class UserPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return UserModel::class;
    }
}
