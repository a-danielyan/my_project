<?php

namespace App\Http\Repositories;

use App\Models\Account;
use App\Models\AccountTraining;
use Illuminate\Database\Eloquent\Collection;

class AccountTrainingRepository extends BaseRepository
{
    /**
     * @param AccountTraining $model
     */
    public function __construct(
        AccountTraining $model,
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
