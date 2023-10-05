<?php

namespace App\Policies;

use App\Models\ContactType;

class ContactTypePolicy extends BasePolicy
{
    protected function entity(): string
    {
        return ContactType::class;
    }
}
