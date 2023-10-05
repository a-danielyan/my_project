<?php

namespace App\Policies;

use App\Models\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use App\Models\User as UserModel;

//@todo CHECK WHAT METHOD WE CAN REMOVE!
abstract class BasePolicy
{
    use HandlesAuthorization;

    protected string|false $groupEntity = false;

    /**
     * Check only write permission for group
     *
     * @var bool
     */
    protected bool $onlyWritePermission = true;

    protected string|false $modelClass = false;

    /**
     * Check access to particular model
     *
     * @param User $user
     * @param Model $model
     * @return bool
     */
    protected function checkAccessToModel(User $user, Model $model): bool
    {
        if (!$this->checkUserAccess($user, $model)) {
            return false;
        }

        return true;
    }

    /**
     * Check if client's user is valid
     *
     * @param User $user
     * @param Model $model
     * @return bool
     */
    protected function checkUserAccess(User $user, Model $model): bool
    {
        return true;
    }

    /**
     * Check access for update
     *
     * @param User $initiator
     * @param Model $model
     * @return bool
     */
    public function update(User $initiator, Model $model): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_UPDATE);
    }

    public function bulkUpdate(User $initiator): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_BULK_UPDATE);
    }

    /**
     * Check access for delete
     *
     * @param User $initiator
     * @param Model $model
     * @return bool
     */
    public function delete(User $initiator, Model $model): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_DELETE);
    }

    /**
     * Check access for view/show
     *
     * @param User $initiator
     * @param Model $model
     * @return bool
     */
    public function view(User $initiator, Model $model): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_READ);
    }

    /**
     * Check access for create/insert
     *
     * @param User $initiator
     * @return bool
     */
    public function create(User $initiator): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_CREATE);
    }

    /**
     * Clone model
     *
     * @param User $initiator
     * @param Model $model
     * @return bool
     */
    public function clone(User $initiator, Model $model): bool
    {
        return $this->checkAccessToModel($initiator, $model);
    }


    public function bulk(User $initiator, string|array $ids): bool
    {
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        foreach ($ids as $id) {
            $model = $this->modelClass::findOrFail($id);

            if (!$this->checkAccessToModel($initiator, $model)) {
                return false;
            }
        }

        return true;
    }

    public function viewAny(User $initiator): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_READ);
    }

    protected function entity(): string
    {
        return '';
    }

    public function restoreItem(User $initiator): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_DELETE);
    }
}
