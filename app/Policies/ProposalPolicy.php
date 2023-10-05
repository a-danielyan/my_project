<?php

namespace App\Policies;

use App\Models\Proposal;

class ProposalPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Proposal::class;
    }
}
