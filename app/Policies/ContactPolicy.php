<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\ContactAttachments;
use App\Models\Permission;
use App\Models\User as UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class ContactPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Contact::class;
    }

    public function addAttachment(User $initiator, Model $model): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_CREATE);
    }

    public function updateAttachment(User $initiator, Model $model, ContactAttachments $contactAttachments): bool
    {
        /** @var UserModel $initiator */
        return $contactAttachments->contact_id === $model->getKey() &&
            $initiator->hasPermissionTo(
                $this->entity(),
                Permission::ACTION_UPDATE,
            );
    }

    public function deleteAttachment(User $initiator, Model $model, ContactAttachments $contactAttachments): bool
    {
        /** @var UserModel $initiator */
        return $contactAttachments->contact_id === $model->getKey() &&
            $initiator->hasPermissionTo(
                $this->entity(),
                Permission::ACTION_DELETE,
            );
    }

    public function deleteBulk(User $initiator): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_DELETE);
    }
}
