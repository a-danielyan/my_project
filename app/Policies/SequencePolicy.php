<?php

namespace App\Policies;

use App\Models\Sequence\Sequence;

class SequencePolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Sequence::class;
    }
}
