<?php

namespace App\Http\Services;

use App\Http\Repositories\EntityLogRepository;
use App\Http\Resource\EntityLogResource;

class EntityLogService extends BaseService
{
    public function __construct(
        EntityLogRepository $entityLogRepository,
    ) {
        $this->repository = $entityLogRepository;
    }

    public function resource(): string
    {
        return EntityLogResource::class;
    }

    public function getAll(array $params, string $entityType, int $entityId): array
    {
        return $this->paginate($this->repository->getAllRecordsForEntity($params, $entityType, $entityId));
    }
}
