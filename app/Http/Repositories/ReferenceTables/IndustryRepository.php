<?php

namespace App\Http\Repositories\ReferenceTables;

use App\Http\Repositories\BaseRepository;
use App\Models\Industry;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndustryRepository extends BaseRepository
{
    /**
     * @param Industry $industry
     */
    public function __construct(Industry $industry)
    {
        $this->model = $industry;
    }

    public function getAll(array $params, User $user): LengthAwarePaginator
    {
        return $this->prepareQueryForGet($params, $user)
            ->paginate($params['limit']);
    }
}
