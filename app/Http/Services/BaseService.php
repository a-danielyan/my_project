<?php

namespace App\Http\Services;

use App\Events\ModelCreated;
use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Helpers\PaginateHelper;
use App\Http\Repositories\BaseRepository;
use App\Models\TagEntityAssociation;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

abstract class BaseService
{
    protected BaseRepository $repository;

    /**
     * Get path of particular resource
     */
    abstract public function resource(): string;

    /**
     * Build pagination
     *
     * @param LengthAwarePaginator $data
     * @param string|null $resource
     * @return array
     */
    public function paginate(LengthAwarePaginator $data, ?string $resource = null): array
    {
        $resource = $resource ?? $this->resource();
        $resultData = $resource::collection($data);

        return $this->preparePaginatedData($resultData, $data);
    }

    /**
     * Show specific resource
     * @param Model $model
     * @param string|null $resource
     * @return JsonResource
     */
    public function show(Model $model, string $resource = null): JsonResource
    {
        $resource = $resource ?? $this->resource();

        return new $resource($model);
    }

    /**
     * @param int $modelId
     * @return Model
     */
    public function findOrFail(int $modelId): Model
    {
        return $this->repository->findOrFail($modelId);
    }

    /**
     * @param mixed $resultData
     * @param LengthAwarePaginator $paginateData
     * @return array
     */
    protected function preparePaginatedData(
        mixed $resultData,
        LengthAwarePaginator $paginateData,
    ): array {
        $paginateObject = new PaginateHelper();
        $paginateData = $paginateData->appends(request()->except('page'));

        return [
                'data' => $resultData,
            ] + $paginateObject->meta($paginateData);
    }


    /**
     * @param array $data
     * @param Authenticatable|User $user
     * @return JsonResource
     * @throws ModelCreateErrorException
     */
    public function store(array $data, Authenticatable|User $user): JsonResource
    {
        DB::beginTransaction();
        $data = $this->beforeStore($data, $user);

        $model = $this->storeModelAndTags($data, $user);
        $this->afterStore($model, $data, $user);
        $resource = $this->resource();

        DB::commit();

        return new $resource($model);
    }

    protected function deleteTagAssociation(int $entityId): void
    {
        TagEntityAssociation::query()->where('entity_id', $entityId)->delete();
    }

    protected function createTags(array $data, int $entityId): void
    {
        $tags = $data['tag'];
        $insertData = collect();

        foreach ($tags as $tag) {
            $insertData->push([
                'tag_id' => $tag['id'],
                'entity' => $data['entity_type'],
                'entity_id' => $entityId,
            ]);
        }

        $insertData = $insertData->unique('tag_id')->all();

        TagEntityAssociation::query()->insert($insertData);
    }

    /**
     * @param array $data
     * @param Model $model
     * @param Authenticatable|User $user
     * @return JsonResource
     * @throws ModelUpdateErrorException
     */
    public function update(array $data, Model $model, Authenticatable|User $user): JsonResource
    {
        $data = $this->beforeUpdate($data, $model, $user);

        if ($this->repository->update($model, $data)) {
            $this->updateTags($data, $model);

            $this->afterUpdate($model, $data, $user);
            $resource = $this->resource();

            return new $resource($model);
        }

        throw new ModelUpdateErrorException();
    }

    /**
     * @param array $data
     * @param Authenticatable|User $user
     * @return Model
     * @throws ModelCreateErrorException
     */
    protected function storeModelAndTags(array $data, Authenticatable|User $user): Model
    {
        $model = $this->repository->create($data);
        if ($this->repository->save($model)) {
            $this->updateTags($data, $model);

            $model->refresh();

            return $model;
        }
        throw new ModelCreateErrorException();
    }

    protected function afterStore(Model $model, array $data, Authenticatable|User $user): void
    {
        ModelCreated::dispatch($model, $user);
    }

    protected function beforeStore(array $data, Authenticatable|User $user): array
    {
        $data['created_by'] = $user->getKey();

        return $data;
    }

    protected function beforeUpdate(array $data, Model $model, Authenticatable|User $user): array
    {
        $data['updated_by'] = $user->getKey();

        return $data;
    }

    protected function afterUpdate(Model $model, array $data, Authenticatable|User $user): void
    {
    }

    protected function updateTags(array $data, Model $model): void
    {
        if (isset($data['tag'])) {
            $this->deleteTagAssociation($model->getKey());
            if (!empty($data['tag']) && is_array($data['tag'])) {
                $this->createTags($data, $model->getKey());
            }
        }
    }

    /**
     * @param int $itemId
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function restoreItem(int $itemId, User|Authenticatable $user): void
    {
        $entity = $this->repository->findById($itemId, true);
        if (!$entity || !$entity->trashed()) {
            throw new CustomErrorException('Record not found', 422);
        }

        if (method_exists($entity, 'restore')) {
            $entity->restore();

            $entity->updated_by = $user->getKey();
            $this->repository->update($entity);
        }
    }

    /**
     * @param array $params
     * @param User|Authenticatable $user
     * @return void
     * @throws ModelDeleteErrorException
     */
    public function bulkDelete(array $params, User|Authenticatable $user): void
    {
        $idList = array_filter(explode(',', $params['ids']));
        $result = true;
        foreach ($idList as $id) {
            $entity = $this->repository->findOrFail($id);
            $result &= $this->delete($entity, $user);
        }

        if (!$result) {
            throw new ModelDeleteErrorException();
        }
    }

    /**
     * @param array $params
     * @param User|Authenticatable $user
     * @return void
     * @throws ModelUpdateErrorException
     */
    public function bulkUpdate(array $params, User|Authenticatable $user): void
    {
        $idList = array_filter(explode(',', $params['ids']));

        foreach ($idList as $id) {
            $instance = $this->repository->findById($id);
            $this->update($params, $instance, $user);
        }
    }

    /**
     * @param Model $model
     * @param Authenticatable $user
     * @return bool
     * @throws ModelDeleteErrorException
     */
    public function delete(Model $model, Authenticatable $user): bool
    {
        if ($this->repository->delete($model)) {
            $this->repository->update(
                $model,
                [
                    'updated_by' => $user->getKey(),
                ],
                true,
            );

            return true;
        }

        throw new ModelDeleteErrorException();
    }
}
