<?php

namespace App\Http\Repositories;

use App\Models\SolutionSetItems;

class SolutionSetItemsRepository extends BaseRepository
{
    /**
     * @param SolutionSetItems $solutionSetItems
     */
    public function __construct(
        SolutionSetItems $solutionSetItems,
    ) {
        $this->model = $solutionSetItems;
    }
}
