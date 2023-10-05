<?php

namespace App\Http\Repositories\ReferenceTables;

use App\Http\Repositories\BaseRepository;
use App\Models\ContactType;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactTypeRepository extends BaseRepository
{
    /**
     * @param ContactType $contactType
     */
    public function __construct(ContactType $contactType)
    {
        $this->model = $contactType;
    }

    public function getAll(array $params, User $user): LengthAwarePaginator
    {
        return $this->prepareQueryForGet($params, $user)
            ->paginate($params['limit']);
    }
}
