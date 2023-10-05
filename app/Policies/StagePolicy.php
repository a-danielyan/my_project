<?php

namespace App\Policies;

use App\Models\Stage;

class StagePolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Stage::class;
    }
}
