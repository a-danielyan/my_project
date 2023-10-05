<?php

namespace App\Http\Repositories;

use App\Models\EntityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EntityLogRepository extends BaseRepository
{
    /**
     * @param EntityLog $entityLog
     */
    public function __construct(EntityLog $entityLog)
    {
        $this->model = $entityLog;
    }

    public function getAllRecordsForEntity(array $params, string $entityType, int $entityId): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with([
            'field' => function ($query) {
                $query->select([
                    'id',
                    'name',
                ]);
            },
            'updatedBy' => function ($query) {
                $query->select([
                    'id',
                    'first_name',
                    'last_name',
                ]);
            },
        ])->where('entity', 'App\Models\\' . $entityType)
            ->where('entity_id', $entityId)->orderBy('created_at', 'desc');

        if (isset($params['limit']) && $params['limit']) {
            return $query->paginate($params['limit']);
        }

        return $query->paginate();
    }
}
