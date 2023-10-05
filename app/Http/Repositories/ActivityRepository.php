<?php

namespace App\Http\Repositories;

use App\Models\Activity;

class ActivityRepository extends BaseRepository
{
    /**
     * @param Activity $activity
     */
    public function __construct(Activity $activity)
    {
        $this->model = $activity;
    }
}
