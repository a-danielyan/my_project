<?php

namespace App\Http\Repositories;

use App\Http\Repositories\CustomField\CustomFieldValueRepository;
use App\Models\Opportunity;

class OpportunityRepository extends BaseRepositoryWithCustomFields
{
    /**
     * @param Opportunity $opportunity
     * @param CustomFieldValueRepository $customFieldValueRepository
     * @param CustomFieldRepository $customFieldRepository
     */
    public function __construct(
        Opportunity $opportunity,
        CustomFieldValueRepository $customFieldValueRepository,
        CustomFieldRepository $customFieldRepository,
    ) {
        parent::__construct($opportunity, $customFieldValueRepository, $customFieldRepository);
    }
}
