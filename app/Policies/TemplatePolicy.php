<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\Template;
use App\Models\User as UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class TemplatePolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Template::class;
    }

    public function view(User $initiator, Model $model): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_READ) &&
            $this->hasTemplateAccess($initiator, $model);
    }

    public function viewAny(User $initiator): bool
    {
        return true;
    }

    private function hasTemplateAccess(User $initiator, Model $model)
    {
        /** @var  Template $model */
        if ($initiator->isMainAdmin()) {
            //admin have all permissions by default
            return true;
        }

        if (in_array($model->entity, [Template::TEMPLATE_TYPE_PROPOSAL, Template::TEMPLATE_TYPE_INVOICE])) {
            return true;
        }

        if ($model->is_shared || $this->checkUserAccess($initiator, $model)) {
            return true;
        }

        return false;
    }

    protected function checkUserAccess(User $user, Model $model): bool
    {
        if ($model->created_by === $user->getKey()) {
            return true;
        }

        return false;
    }

    public function update(User $initiator, Model $model): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_UPDATE) &&
            $this->checkUserAccess($initiator, $model);
    }

    public function delete(User $initiator, Model $model): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_DELETE) &&
            $this->checkUserAccess($initiator, $model);
    }
}
