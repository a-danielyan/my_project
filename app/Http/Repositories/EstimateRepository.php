<?php

namespace App\Http\Repositories;

use App\Http\Repositories\CustomField\CustomFieldValueRepository;
use App\Models\Estimate;

class EstimateRepository extends BaseRepositoryWithCustomFields
{
    /**
     * @param Estimate $estimate
     * @param CustomFieldValueRepository $customFieldValueRepository
     * @param CustomFieldRepository $customFieldRepository
     */
    public function __construct(
        Estimate $estimate,
        CustomFieldValueRepository $customFieldValueRepository,
        CustomFieldRepository $customFieldRepository,
    ) {
        parent::__construct($estimate, $customFieldValueRepository, $customFieldRepository);
    }
}
