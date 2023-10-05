<?php

namespace App\Http\Repositories;

use App\Http\Repositories\CustomField\CustomFieldValueRepository;
use App\Models\Lead;

class LeadRepository extends BaseRepositoryWithCustomFields
{
    /**
     * @param Lead $lead
     * @param CustomFieldValueRepository $customFieldValueRepository
     * @param CustomFieldRepository $customFieldRepository
     */
    public function __construct(
        Lead $lead,
        CustomFieldValueRepository $customFieldValueRepository,
        CustomFieldRepository $customFieldRepository,
    ) {
        parent::__construct($lead, $customFieldValueRepository, $customFieldRepository);
    }
}
