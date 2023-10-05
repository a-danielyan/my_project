<?php

namespace App\Http\Repositories\ReferenceTables;

use App\Http\Repositories\BaseRepository;
use App\Models\Solutions;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SolutionRepository extends BaseRepository
{
    /**
     * @param Solutions $solutions
     */
    public function __construct(Solutions $solutions)
    {
        $this->model = $solutions;
    }

    public function getAll(array $params, User $user): LengthAwarePaginator
    {
        return $this->prepareQueryForGet($params, $user)
            ->paginate($params['limit']);
    }
}
