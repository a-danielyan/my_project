<?php

namespace App\Policies;

use App\Models\Role;

class RolePolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Role::class;
    }
}
