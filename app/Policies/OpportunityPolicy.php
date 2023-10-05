<?php

namespace App\Policies;

use App\Models\Opportunity;
use App\Models\OpportunityAttachment;
use App\Models\Permission;
use App\Models\User as UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class OpportunityPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Opportunity::class;
    }

    public function deleteBulk(User $initiator): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_DELETE);
    }

    public function addAttachment(User $initiator, Model $model): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_CREATE);
    }

    public function updateAttachment(User $initiator, Model $model, OpportunityAttachment $opportunityAttachment): bool
    {
        /** @var UserModel $initiator */
        return $opportunityAttachment->opportunity_id === $model->getKey() &&
            $initiator->hasPermissionTo(
                $this->entity(),
                Permission::ACTION_UPDATE,
            );
    }

    public function deleteAttachment(User $initiator, Model $model, OpportunityAttachment $opportunityAttachment): bool
    {
        /** @var UserModel $initiator */
        return $opportunityAttachment->opportunity_id === $model->getKey() &&
            $initiator->hasPermissionTo(
                $this->entity(),
                Permission::ACTION_DELETE,
            );
    }
}
