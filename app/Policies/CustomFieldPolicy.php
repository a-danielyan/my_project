<?php

namespace App\Policies;

use App\Models\CustomField;

class CustomFieldPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return CustomField::class;
    }
}
