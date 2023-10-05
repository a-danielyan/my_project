<?php

namespace App\Http\Repositories;

use App\Models\Account;
use App\Models\AccountDemo;
use Illuminate\Database\Eloquent\Collection;

class AccountDemoRepository extends BaseRepository
{
    /**
     * @param AccountDemo $model
     */
    public function __construct(
        AccountDemo $model,
    ) {
        $this->model = $model;
    }

    public function getAllForAccount(Account $account): Collection|array
    {
        $query = $this->model->newQuery()->with(['activity', 'createdBy', 'updatedBy', 'trainedBy'])
            ->where('account_id', $account->getKey());

        return $query->get();
    }
}
