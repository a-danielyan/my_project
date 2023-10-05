<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\AccountTraining;
use Illuminate\Foundation\Auth\User;

class AccountTrainingPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return AccountTraining::class;
    }

    public function trainingAndAccountMatch(User $initiator, AccountTraining $training, Account $account): bool
    {
        return $training->account_id == $account->getKey();
    }
}
