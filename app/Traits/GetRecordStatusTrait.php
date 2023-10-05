<?php

namespace App\Traits;

use App\Models\User;

trait GetRecordStatusTrait
{
    private function getStatus(): string
    {
        if ($this->trashed()) {
            return User::STATUS_DISABLED;
        } else {
            return $this->status;
        }
    }
}
