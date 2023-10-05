<?php

namespace App\Http\Repositories;

use App\Models\SolutionSet;

class SolutionSetRepository extends BaseRepository
{
    /**
     * @param SolutionSet $solution_set
     */
    public function __construct(
        SolutionSet $solution_set,
    ) {
        $this->model = $solution_set;
    }
}
