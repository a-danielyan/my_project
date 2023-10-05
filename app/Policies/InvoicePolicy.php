<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use App\Models\Permission;
use App\Models\User as UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class InvoicePolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Invoice::class;
    }

    public function addAttachment(User $initiator, Model $model): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_CREATE);
    }

    public function updateAttachment(User $initiator, Model $model, InvoiceAttachment $invoiceAttachment): bool
    {
        /** @var UserModel $initiator */
        return $invoiceAttachment->invoice_id === $model->getKey() &&
            $initiator->hasPermissionTo(
                $this->entity(),
                Permission::ACTION_UPDATE,
            );
    }

    public function deleteAttachment(User $initiator, Model $model, InvoiceAttachment $invoiceAttachment): bool
    {
        /** @var UserModel $initiator */
        return $invoiceAttachment->invoice_id === $model->getKey() &&
            $initiator->hasPermissionTo(
                $this->entity(),
                Permission::ACTION_DELETE,
            );
    }
}
