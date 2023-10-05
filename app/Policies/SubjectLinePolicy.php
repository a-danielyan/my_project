<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\SubjectLine;
use App\Models\User as UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class SubjectLinePolicy extends BasePolicy
{
    protected function entity(): string
    {
        return SubjectLine::class;
    }

    public function update(User $initiator, Model $model): bool
    {
        /** @var UserModel $initiator */
        if ($initiator->isMainAdmin()) {
            //admin have all permissions by default
            return true;
        }


        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_UPDATE)
            && $initiator->getKey() === $model->created_by;
    }


    public function delete(User $initiator, Model $model): bool
    {
        /** @var UserModel $initiator */
        if ($initiator->isMainAdmin()) {
            //admin have all permissions by default
            return true;
        }

        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_DELETE) &&
            $initiator->getKey() === $model->created_by;
    }
}
