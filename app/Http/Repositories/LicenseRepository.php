<?php

namespace App\Http\Repositories;

use App\Models\License;

class LicenseRepository extends BaseRepository
{
    /**
     * @param License $license
     */
    public function __construct(
        License $license,
    ) {
        $this->model = $license;
    }
}
