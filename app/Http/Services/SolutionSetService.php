<?php

namespace App\Http\Services;

use App\Http\Repositories\SolutionSetItemsRepository;
use App\Http\Repositories\SolutionSetRepository;
use App\Http\Resource\SolutionSetResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\Json\JsonResource;

class SolutionSetService extends BaseService
{
    public function __construct(
        SolutionSetRepository $solutionSetRepository,
        private SolutionSetItemsRepository $solutionSetItemsRepository,
    ) {
        $this->repository = $solutionSetRepository;
    }

    public function resource(): string
    {
        return SolutionSetResource::class;
    }

    /**
     * @param array $params
     * @param Authenticatable|User $user
     * @return array
     */
    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->get($user, $params, relation: ['items']));
    }

    public function show(Model $model, string $resource = null): JsonResource
    {
        $model->load(['items']);
        $resource = $resource ?? $this->resource();

        return new $resource($model);
    }

    protected function afterStore(Model $model, array $data, Authenticatable|User $user): void
    {
        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                $this->solutionSetItemsRepository->create([
                    'solution_set_id' => $model->getKey(),
                    'product_id' => $item['productId'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'description' => $item['description'] ?? null,
                    'discount' => $item['discount'] ?? 0,
                ]);
            }
        }
        parent::afterStore($model, $data, $user);
    }

    protected function afterUpdate(Model $model, array $data, Authenticatable|User $user): void
    {
        if (isset($data['items'])) {
            $this->solutionSetItemsRepository->deleteByParams(['solution_set_id' => $model->getKey()], true);
            foreach ($data['items'] as $item) {
                $this->solutionSetItemsRepository->create([
                    'solution_set_id' => $model->getKey(),
                    'product_id' => $item['productId'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'description' => $item['description'] ?? null,
                    'discount' => $item['discount'] ?? 0,
                ]);
            }
        }
    }
}
