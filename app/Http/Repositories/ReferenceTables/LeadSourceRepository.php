<?php

namespace App\Http\Repositories\ReferenceTables;

use App\Http\Repositories\BaseRepository;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LeadSourceRepository extends BaseRepository
{
    /**
     * @param LeadSource $leadSource
     */
    public function __construct(LeadSource $leadSource)
    {
        $this->model = $leadSource;
    }

    public function getAll(array $params, User $user): LengthAwarePaginator
    {
        return $this->prepareQueryForGet($params, $user)
            ->paginate($params['limit']);
    }
}
