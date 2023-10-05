<?php

namespace App\Policies;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class TagPolicy extends BasePolicy
{
    protected function entity(): string
    {
        return Tag::class;
    }

    public function create(User $initiator): bool
    {
        return $initiator->isMainAdmin();
    }

    public function update(User $initiator, Model $model): bool
    {
        return $initiator->isMainAdmin();
    }

    public function delete(User $initiator, Model $model): bool
    {
        return $initiator->isMainAdmin();
    }
}
