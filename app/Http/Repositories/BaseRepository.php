<?php

namespace App\Http\Repositories;

use App\Events\ModelChanged;
use App\Models\CustomField;
use App\Models\Interfaces\FilteredInterface;
use App\Models\Interfaces\FilterStrategy;
use App\Models\Interfaces\WithGroupPermissionInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class BaseRepository
 * @package App\Http\Repositories
 */
abstract class BaseRepository
{
    protected Model|Builder $model;
    protected ?int $updateId = null;

    /**
     * Find by id
     *
     * @param int $id
     * @param bool $isTrashed
     * @param array $relation
     * @param array $where
     * @param array $select
     * @return Model|null
     */
    public function findById(
        int $id,
        bool $isTrashed = false,
        array $where = [],
        array $relation = [],
        array $select = ['*'],
    ): ?Model {
        $model = $this->model;

        if ($isTrashed) {
            $model = $model->withTrashed();
        }

        if (!empty($select)) {
            $model = $model->select($select);
        }

        if (!empty($where)) {
            $model = $model->where($where);
        }

        if (!empty($relation)) {
            $model = $model->with($relation);
        }

        return $model
            ->find($id);
    }

    /**
     * Find or fail
     *
     * @param int $id
     * @return Model
     */
    public function findOrFail(int $id): Model
    {
        return $this
            ->model
            ->findOrFail($id);
    }

    public function firstOrFail(
        User $user = null,
        array $params = [],
        array $relation = [],
        array $where = [],
        array $whereIn = [],
        array $whereHas = [],
    ): Model {
        return $this
            ->getQuery(...func_get_args())
            ->firstOrFail();
    }


    /**
     * Create one model
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this
            ->model
            ->create($data);
    }

    /**
     * Insert many models by array
     *
     * @param array $dataOfModels
     * @return bool
     */
    public function insert(array $dataOfModels): bool
    {
        return $this->model->insert($dataOfModels);
    }

    /**
     * Update
     *
     * @param Model $model
     * @param array $data
     * @param bool $isTrashed
     * @return bool
     */
    public function update(
        Model $model,
        array $data = [],
        bool $isTrashed = false,
    ): bool {
        if ($isTrashed && method_exists($model, 'withTrashed')) {
            $model->withTrashed();
        }

        return $this->logChanges($model, $data);
    }

    public function logChanges(Model $model, array $data): bool
    {
        $model->fill($data);
        $changes = $model->getDirty();
        $originalValues = $model->getOriginal();
        $model->save();
        if (count($changes) > 0) {
            $this->updateId = time();
            $changedEntityLog = [];

            foreach ($changes as $field => $value) {
                /** @var CustomField $customField */
                $customField = CustomField::query()->where('code', $field)
                    ->where('entity_type', $model::class)->select(['id'])->first();
                if ($customField) {
                    $changedEntityLog[] = [
                        'entity' => $model::class,
                        'entity_id' => $model->getKey(),
                        'field_id' => $customField->id,
                        'previous_value' => $originalValues[$field],
                        'new_value' => $value,
                        'updated_by' => $data['updated_by'],
                        'update_id' => $this->updateId,
                        'created_at' => now(),
                    ];
                }
            }
            ModelChanged::dispatch($changedEntityLog);
        }

        return true;
    }


    public function updateByParams(
        array $whereData = [],
        array $updateData = [],
    ): bool {
        return $this
            ->model
            ->where($whereData)
            ->update($updateData);
    }

    /**
     * Delete model
     *
     * @param Model $model
     * @return bool
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * Delete model by params
     * @param array $params
     * @param bool $forceDelete
     * @return bool
     */
    public function deleteByParams(array $params, bool $forceDelete = false): bool
    {
        $records = $this->model->where($params);

        if (!$forceDelete) {
            return $records->delete();
        }

        return $records->forceDelete();
    }

    /**
     * Save by model
     *
     * @param Model $model
     * @return bool
     */
    public function save(Model $model): bool
    {
        return $model
            ->save();
    }

    /**
     * @param User|null $user
     * @param array $params
     * @param array $relation
     * @param array $where
     * @param array $whereIn
     * @param array $whereHas
     * @return LengthAwarePaginator|Builder[]|Collection
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
     * @param User|null $user
     * @param array $params
     * @param array $relation
     * @param array $where
     * @return int
     */
    public function getCount(
        User $user = null,
        array $params = [],
        array $relation = [],
        array $where = [],
    ): int {
        return $this->getQuery($user, $params, $relation, $where)->count();
    }

    /**
     * @param User|null $user
     * @param array $params
     * @param array $relation
     * @param array $where
     * @param array $whereIn
     * @param array $whereHas // ['relation' => Closure, 'relation' => Closure]
     * @return Builder
     */
    protected function getQuery(
        User $user = null,
        array $params = [],
        array $relation = [],
        array $where = [],
        array $whereIn = [],
        array $whereHas = [],
    ): Builder {
        $query = $this->prepareQueryForGet($params, $user);

        if (!empty($whereHas)) {
            foreach ($whereHas as $relation => $closureOrRelation) {
                if (is_numeric($relation)) {
                    $query = $query->whereHas($closureOrRelation);
                } else {
                    $query = $query->whereHas($relation, $closureOrRelation);
                }
            }
        }

        if (!empty($where)) {
            $query = $query->where($where);
        }

        if (!empty($whereIn)) {
            $query = $this->whereInQuery($query, $whereIn);
        }

        if (!empty($relation)) {
            $query = $query->with($relation);
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    protected function whereInQuery(
        Builder $query,
        array $params = [],
    ): Builder {
        foreach ($params as $param) {
            if (!empty($param['values']) && $param['field']) {
                $query->whereIn($param['field'], $param['values']);
            }
        }

        return $query;
    }

    public function getCountByParams(User $user = null, array $where = []): int
    {
        return $this->prepareQueryForGet([], $user, null, true)
            ->where($where)
            ->count();
    }

    /**
     * Get first
     * @param array $params
     * @param array $relation
     * @param array $where
     * @param User|null $user
     * @param bool $isTrashed
     * @return Model|null
     */
    public function first(
        array $params = [],
        array $relation = [],
        array $where = [],
        User $user = null,
        bool $isTrashed = false,
    ): ?Model {
        $query = $this->getQuery($user, $params, $relation, $where);

        if ($isTrashed) {
            $query->withTrashed();
        }

        return $query->first();
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

        $query->select($model->getTable() . '.*');

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
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                if (in_array(User::STATUS_DISABLED, $params['status'])) {
                    $query->withTrashed();
                }
            } else {
                if ($params['status'] === User::STATUS_DISABLED) {
                    $query->withTrashed();
                }
            }
        }

        return $query;
    }


    /**
     * @param array $attributes
     * @param array $values
     * @return Model
     */
    public function firstOrCreate(array $attributes, array $values = []): Model
    {
        return $this->model->firstOrCreate($attributes, $values);
    }

    protected function filterQueryByPermission(
        mixed $query,
        ?\Illuminate\Foundation\Auth\User $user = null,
        ?bool $checkWritePermission = false,
    ): mixed {
        if (
            $user instanceof User &&
            $query->getModel() instanceof WithGroupPermissionInterface
        ) {
            $query->permission($user, true, $checkWritePermission);
        }

        return $query;
    }

    public function updateOrCreate(array $where, array $data): Model|Builder
    {
        return $this->model->newQuery()->updateOrCreate($where, $data);
    }
}
