<?php

namespace App\Policies;

use App\Models\License;

class LicensePolicy extends BasePolicy
{
    protected function entity(): string
    {
        return License::class;
    }
}
