<?php

namespace App\Policies;

use App\Models\LeadStatus;

class LeadStatusPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return LeadStatus::class;
    }
}
