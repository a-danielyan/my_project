<?php

namespace App\Http\Repositories\ReferenceTables;

use App\Http\Repositories\BaseRepository;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StageRepository extends BaseRepository
{
    /**
     * @param Stage $stage
     */
    public function __construct(Stage $stage)
    {
        $this->model = $stage;
    }

    public function getAll(array $params, User $user): LengthAwarePaginator
    {
        return $this->prepareQueryForGet($params, $user)
            ->paginate($params['limit']);
    }
}
