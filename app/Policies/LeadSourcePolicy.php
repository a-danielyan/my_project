<?php

namespace App\Policies;

use App\Models\LeadSource;

class LeadSourcePolicy extends BasePolicy
{
    protected function entity(): string
    {
        return LeadSource::class;
    }
}
