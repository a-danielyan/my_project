<?php

namespace App\Http\Repositories;

use App\Exceptions\CustomErrorException;
use App\Models\Email;
use App\Models\Interfaces\FilteredInterface;
use App\Models\Interfaces\FilterStrategy;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailRepository extends BaseRepository
{
    /**
     * @param Email $email
     */
    public function __construct(
        Email $email,
    ) {
        $this->model = $email;
    }


    /**
     * @param User|null $user
     * @param array $params
     * @param array $relation
     * @param array $where
     * @param array $whereIn
     * @param array $whereHas
     * @return Collection|LengthAwarePaginator|array|Builder[]
     * @throws CustomErrorException
     */
    public function get(
        User $user = null,
        array $params = [],
        array $relation = [],
        array $where = [],
        array $whereIn = [],
        array $whereHas = [],
    ): Collection|LengthAwarePaginator|array {
        $query = $this->getQuery($user, $params, $relation, $where, $whereIn, $whereHas);

        if (isset($params['limit']) && $params['limit']) {
            return $query->paginate($params['limit']);
        }

        return $query->get();
    }

    /**
     * @param array $params
     * @param User|null $user
     * @param Model|null $customModel
     * @param bool $forCount
     * @return Builder
     */
    protected function prepareQueryForGet(
        array $params = [],
        ?User $user = null,
        Model $customModel = null,
        bool $forCount = false,
    ): Builder {
        $model = $customModel ?? $this->model;

        $query = $model->newQuery();

        $query->select([
            'id',
            'email_id',
            'token_id',
            'received_date',
            'from',
            'to',
            'subject',
            'status',
        ]);

        if (!empty($params) && $model instanceof FilteredInterface) {
            /** @var FilteredInterface $query */
            $query->filter($params);
        }

        $query = self::filterQueryByPermission($query, $user, $params['hasWritePermission'] ?? false);

        if (
            array_key_exists('distinct', $params) &&
            !empty($params['fields']) && $model instanceof FilteredInterface
        ) {
            if ($this->model instanceof FilterStrategy) {
                $filterArray = $this->model->filterStrategy()->filterArray();
            } else {
                $filterArray = $this->model->filterArray();
            }
            foreach (explode(',', $params['fields']) as $field) {
                $field = trim($field);
                $snakeField = Str::snake($field);
                if (
                    in_array($field, $filterArray['equal']) || in_array($field, $filterArray['like'])
                    || in_array($snakeField, $filterArray['equal']) || in_array($snakeField, $filterArray['like'])
                ) {
                    $query->groupBy($snakeField);
                } elseif (isset($filterArray['field_relation'][$field])) {
                    $relationFunction = $filterArray['field_relation'][$field]['relationFunction'];
                    $model->{$relationFunction}($query, $field);
                }
            }
        } else {
            if (!$forCount) {
                $query->groupBy($model->getTable() . '.id');
            }
        }

        if ($model instanceof FilteredInterface) {
            if (!$forCount) {
                $query->orderBy($model->getTable() . '.id');
            }
        }

        return $query;
    }
}
