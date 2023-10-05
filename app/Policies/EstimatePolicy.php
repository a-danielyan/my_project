<?php

namespace App\Policies;

use App\Models\Estimate;
use App\Models\EstimateAttachment;
use App\Models\Permission;
use App\Models\User as UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class EstimatePolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Estimate::class;
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

    public function updateAttachment(User $initiator, Model $model, EstimateAttachment $attachment): bool
    {
        /** @var UserModel $initiator */
        return $attachment->estimate_id === $model->getKey() &&
            $initiator->hasPermissionTo(
                $this->entity(),
                Permission::ACTION_UPDATE,
            );
    }

    public function deleteAttachment(User $initiator, Model $model, EstimateAttachment $attachment): bool
    {
        /** @var UserModel $initiator */
        return $attachment->estimate_id === $model->getKey() &&
            $initiator->hasPermissionTo(
                $this->entity(),
                Permission::ACTION_DELETE,
            );
    }
}
