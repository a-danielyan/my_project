<?php

namespace App\Http\Repositories;

use App\Models\Preference;

class PreferenceRepository extends BaseRepository
{
    /**
     * @param Preference $preference
     */
    public function __construct(Preference $preference)
    {
        $this->model = $preference;
    }
}
