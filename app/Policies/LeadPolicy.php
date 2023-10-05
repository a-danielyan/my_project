<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\LeadAttachments;
use App\Models\Permission;
use App\Models\User as UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class LeadPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Lead::class;
    }

    public function convertToContactAccount(User $initiator, Model $model): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo(Contact::class, Permission::ACTION_CREATE) &&
            $initiator->hasPermissionTo(Account::class, Permission::ACTION_CREATE);
    }

    public function bulkDeletes(User $initiator): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_DELETE);
    }

    public function addAttachment(User $initiator, Model $model): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_CREATE);
    }

    public function updateAttachment(User $initiator, Model $model, LeadAttachments $leadAttachment): bool
    {
        /** @var UserModel $initiator */
        return $leadAttachment->lead_id === $model->getKey() &&
            $initiator->hasPermissionTo(
                $this->entity(),
                Permission::ACTION_UPDATE,
            );
    }

    public function deleteAttachment(User $initiator, Model $model, LeadAttachments $leadAttachment): bool
    {
        /** @var UserModel $initiator */
        return $leadAttachment->lead_id === $model->getKey() &&
            $initiator->hasPermissionTo(
                $this->entity(),
                Permission::ACTION_DELETE,
            );
    }
}
