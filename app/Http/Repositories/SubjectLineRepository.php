<?php

namespace App\Http\Repositories;

use App\Models\Role;
use App\Models\SubjectLine;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SubjectLineRepository extends BaseRepository
{
    /**
     * @param SubjectLine $subjectLine
     */
    public function __construct(SubjectLine $subjectLine)
    {
        $this->model = $subjectLine;
    }


    public function getAllForUser(Authenticatable|User $user, array $params): Collection|LengthAwarePaginator|array
    {
        $query = $this->prepareQueryForGet($params, $user);

        $query->where(function ($query) use ($user) {
            $query->whereHas('createdBy', function ($query) {
                $query->whereHas('role', function ($query) {
                    $query->where('name', Role::MAIN_ADMINISTRATOR_ROLE);
                });
            })->orWhere('created_by', $user->getKey());
        });


        if (isset($params['limit']) && $params['limit']) {
            return $query->paginate($params['limit']);
        }

        return $query->get();
    }
}
