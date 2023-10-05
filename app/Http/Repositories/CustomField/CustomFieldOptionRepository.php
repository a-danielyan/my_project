<?php

namespace App\Http\Repositories\CustomField;

use App\Http\Repositories\BaseRepository;
use App\Models\CustomFieldOption;

class CustomFieldOptionRepository extends BaseRepository
{
    public function __construct(CustomFieldOption $customFieldOption)
    {
        $this->model = $customFieldOption;
    }
}
