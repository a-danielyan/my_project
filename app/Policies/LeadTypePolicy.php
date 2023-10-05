<?php

namespace App\Policies;

use App\Models\LeadType;

class LeadTypePolicy extends BasePolicy
{
    protected function entity(): string
    {
        return LeadType::class;
    }
}
