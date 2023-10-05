<?php

namespace App\Listeners;

use App\Events\ModelChanged;
use App\Http\Repositories\EntityLogRepository;

class WriteModelChangedEntityLog
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
    public function handle(ModelChanged $event): void
    {
        $this->entityLogRepository->insert($event->changedEntity);
    }
}
