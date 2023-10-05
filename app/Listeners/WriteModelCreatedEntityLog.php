<?php

namespace App\Listeners;

use App\Events\ModelCreated;
use App\Http\Repositories\EntityLogRepository;
use App\Models\EntityLog;

class WriteModelCreatedEntityLog
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
    public function handle(ModelCreated $event): void
    {
        $updateLogData = [
            'entity' => $event->model::class,
            'entity_id' => $event->model->getKey(),
            'field_id' => null,
            'previous_value' => null,
            'new_value' => 'Entity created',
            'updated_by' => $event->user->getKey(),
            'update_id' => time(),
            'log_type' => EntityLog::CREATE_LOG_TYPE,
        ];

        $this->entityLogRepository->create($updateLogData);
    }
}
