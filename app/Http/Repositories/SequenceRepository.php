<?php

namespace App\Http\Repositories;

use App\Models\Sequence\Sequence;

class SequenceRepository extends BaseRepository
{
    /**
     * @param Sequence $sequence
     */
    public function __construct(
        Sequence $sequence,
    ) {
        $this->model = $sequence;
    }
}
