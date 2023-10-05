<?php

namespace App\Http\Repositories\ReferenceTables;

use App\Http\Repositories\BaseRepository;
use App\Models\LeadType;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LeadTypeRepository extends BaseRepository
{
    /**
     * @param LeadType $leadType
     */
    public function __construct(LeadType $leadType)
    {
        $this->model = $leadType;
    }

    public function getAll(array $params, User $user): LengthAwarePaginator
    {
        return $this->prepareQueryForGet($params, $user)
            ->paginate($params['limit']);
    }
}
