<?php

namespace App\Http\Requests\Tag;

use App\Http\Requests\BaseGetFormRequest;

class TagGetRequest extends BaseGetFormRequest
{
    protected const MULTI_SELECT_FIELDS = [
        'status',
        'entityType',
    ];
}
