<?php

namespace App\Http\Requests\License;

use App\Http\Requests\BaseGetFormRequest;

class LicenseGetRequest extends BaseGetFormRequest
{
    protected const MULTI_SELECT_FIELDS = [
        'status',
        'licenseType',
    ];
}
