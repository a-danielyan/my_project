<?php

namespace App\Policies;

use App\Models\AccountPartnershipStatus;

class AccountPartnershipPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return AccountPartnershipStatus::class;
    }
}
