<?php

namespace App\Http\Repositories;

use App\Models\Reminder;

class ReminderRepository extends BaseRepository
{
    /**
     * @param Reminder $reminder
     */
    public function __construct(
        Reminder $reminder,
    ) {
        $this->model = $reminder;
    }
}
