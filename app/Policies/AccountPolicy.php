<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\AccountAttachment;
use App\Models\Permission;
use App\Models\User as UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class AccountPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Account::class;
    }

    public function addAttachment(User $initiator, Model $model): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_CREATE);
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

    public function deleteAttachment(User $initiator, Model $model, AccountAttachment $accountAttachment): bool
    {
        /** @var UserModel $initiator */
        return $accountAttachment->account_id === $model->getKey() &&
            $initiator->hasPermissionTo(
                $this->entity(),
                Permission::ACTION_DELETE,
            );
    }

    public function bulkDeletes(User $initiator): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_DELETE);
    }
}
