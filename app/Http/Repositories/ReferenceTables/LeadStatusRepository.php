<?php

namespace App\Http\Repositories\ReferenceTables;

use App\Http\Repositories\BaseRepository;
use App\Models\LeadStatus;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LeadStatusRepository extends BaseRepository
{
    /**
     * @param LeadStatus $leadStatus
     */
    public function __construct(LeadStatus $leadStatus)
    {
        $this->model = $leadStatus;
    }

    public function getAll(array $params, User $user): LengthAwarePaginator
    {
        return $this->prepareQueryForGet($params, $user)
            ->paginate($params['limit']);
    }
}
