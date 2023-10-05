<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductAttachment;
use App\Models\User as UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class ProductPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Product::class;
    }


    public function updateAttachment(User $initiator, Model $model, ProductAttachment $productAttachment): bool
    {
        /** @var UserModel $initiator */
        return $productAttachment->product_id === $model->getKey() &&
            $initiator->hasPermissionTo(
                $this->entity(),
                Permission::ACTION_UPDATE,
            );
    }

    public function deleteAttachment(User $initiator, Model $model, ProductAttachment $productAttachment): bool
    {
        /** @var UserModel $initiator */
        return $productAttachment->product_id === $model->getKey() &&
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
