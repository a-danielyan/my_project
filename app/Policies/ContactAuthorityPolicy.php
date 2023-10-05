<?php

namespace App\Policies;

use App\Models\ContactAuthority;

class ContactAuthorityPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return ContactAuthority::class;
    }
}
