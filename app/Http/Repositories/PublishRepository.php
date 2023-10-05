<?php

namespace App\Http\Repositories;

use App\Models\PublishDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PublishRepository extends BaseRepository
{
    /**
     * @param PublishDetail $publishDetail
     */
    public function __construct(PublishDetail $publishDetail)
    {
        $this->model = $publishDetail;
    }

    /**
     * @param string $token
     * @return Model|null
     */
    public function findToken(string $token): ?Model
    {
        return $this->model->newQuery()
            ->where('token', $token)
            ->first();
    }


    /**
     * @param string $token
     * @param string $entity
     * @return Model|null
     */
    public function getValidToken(string $token, string $entity): Model|null
    {
        return $this->model->newQuery()
            ->where('token', $token)
            ->where(function (Builder $query) {
                $query->where('expire_on', '>', Carbon::now());
                $query->orWhereNull('expire_on');
            })
            ->where('entity_type', $entity)
            ->first();
    }
}
