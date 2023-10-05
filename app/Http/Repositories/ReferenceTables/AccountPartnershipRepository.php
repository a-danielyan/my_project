<?php

namespace App\Http\Repositories\ReferenceTables;

use App\Http\Repositories\BaseRepository;
use App\Models\AccountPartnershipStatus;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AccountPartnershipRepository extends BaseRepository
{
    /**
     * @param AccountPartnershipStatus $accountPartnershipStatus
     */
    public function __construct(AccountPartnershipStatus $accountPartnershipStatus)
    {
        $this->model = $accountPartnershipStatus;
    }

    public function getAll(array $params, User $user): LengthAwarePaginator
    {
        return $this->prepareQueryForGet($params, $user)
            ->paginate($params['limit']);
    }
}
