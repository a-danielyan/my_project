<?php

namespace App\Listeners;

use App\Events\ModelDeleted;
use App\Http\Repositories\EntityLogRepository;
use App\Models\EntityLog;

class WriteModelDeletedEntityLog
{
    /**
     * Create the event listener.
     */
    public function __construct(private EntityLogRepository $entityLogRepository)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ModelDeleted $event): void
    {
        $updateLogData = [
            'entity' => $event->model::class,
            'entity_id' => $event->model->getKey(),
            'field_id' => null,
            'previous_value' => null,
            'new_value' => 'Entity deleted',
            'updated_by' => $event->user->getKey(),
            'update_id' => time(),
            'log_type' => EntityLog::DELETE_LOG_TYPE,
        ];

        $this->entityLogRepository->create($updateLogData);
    }
}
