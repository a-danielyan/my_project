<?php

namespace App\Http\Repositories\ReferenceTables;

use App\Http\Repositories\BaseRepository;
use App\Models\ContactAuthority;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactAuthorityRepository extends BaseRepository
{
    /**
     * @param ContactAuthority $contactAuthority
     */
    public function __construct(ContactAuthority $contactAuthority)
    {
        $this->model = $contactAuthority;
    }

    public function getAll(array $params, User $user): LengthAwarePaginator
    {
        return $this->prepareQueryForGet($params, $user)
            ->paginate($params['limit']);
    }
}
