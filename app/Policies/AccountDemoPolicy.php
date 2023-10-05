<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\AccountAttachment;
use App\Models\AccountDemo;
use App\Models\Permission;
use App\Models\User as UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class AccountDemoPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return AccountDemo::class;
    }

    public function updateAttachment(User $initiator, Model $model, AccountAttachment $accountAttachment): bool
    {
        /** @var UserModel $initiator */
        return $accountAttachment->account_id === $model->getKey() &&
            $initiator->hasPermissionTo(
                $this->entity(),
                Permission::ACTION_UPDATE,
            );
    }

    public function demoAndAccountMatch(User $initiator, AccountDemo $demo, Account $account): bool
    {
        return $demo->account_id == $account->getKey();
    }
}
