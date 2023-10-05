<?php

namespace App\Policies;

use App\Models\Email;
use App\Models\Permission;
use App\Models\User as UserModel;
use Illuminate\Foundation\Auth\User;

class EmailPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Email::class;
    }

    public function getEmails(User $initiator): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_READ);
    }

    public function showEmail(User $initiator, Email $email): bool
    {
        /** @var UserModel $initiator */
        return $initiator->hasPermissionTo($this->entity(), Permission::ACTION_READ) &&
            $this->checkUserToken($initiator, $email);
    }

    public function sendEmail(User $initiator): bool
    {
        /** @var UserModel $initiator */
        return
            $initiator->hasPermissionTo(
                $this->entity(),
                Permission::ACTION_CREATE,
            );
    }

    private function checkUserToken(User $initiator, Email $email): bool
    {
        $emailToken = $email->token;

        return $emailToken->user_id == $initiator->getKey();
    }
}
