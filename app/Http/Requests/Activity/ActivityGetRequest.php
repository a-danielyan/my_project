<?php

namespace App\Http\Requests\Activity;

use App\Http\Requests\BaseGetFormRequest;

class ActivityGetRequest extends BaseGetFormRequest
{
    protected const MULTI_SELECT_FIELDS = [
        'status',
        'activityStatus',
        'activityType',
    ];
}
