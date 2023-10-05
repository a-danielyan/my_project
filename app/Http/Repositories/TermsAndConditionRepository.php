<?php

namespace App\Http\Repositories;

use App\Models\TermsAndConditions;

class TermsAndConditionRepository extends BaseRepository
{
    /**
     * @param TermsAndConditions $termsAndConditions
     */
    public function __construct(TermsAndConditions $termsAndConditions)
    {
        $this->model = $termsAndConditions;
    }
}
