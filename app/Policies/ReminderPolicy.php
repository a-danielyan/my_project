<?php

namespace App\Policies;

use App\Models\Reminder;

class ReminderPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Reminder::class;
    }
}
