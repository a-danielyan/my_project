<?php

namespace App\Http\Services;

use App\Http\Repositories\ReminderRepository;
use App\Http\Resource\ReminderResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\Json\JsonResource;

class RemindersService extends BaseService
{
    public function __construct(
        ReminderRepository $reminderRepository,
    ) {
        $this->repository = $reminderRepository;
    }

    public function resource(): string
    {
        return ReminderResource::class;
    }

    /**
     * @param array $params
     * @param Authenticatable|User $user
     * @return array
     */
    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->get($user, $params));
    }

    public function show(Model $model, string $resource = null): JsonResource
    {
        // $model->load(['items']);
        $resource = $resource ?? $this->resource();

        return new $resource($model);
    }
}
