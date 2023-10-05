<?php

namespace App\Http\Repositories;

use App\Http\Repositories\CustomField\CustomFieldValueRepository;
use App\Models\Contact;

class ContactRepository extends BaseRepositoryWithCustomFields
{
    /**
     * @param Contact $contact
     * @param CustomFieldValueRepository $customFieldValueRepository
     * @param CustomFieldRepository $customFieldRepository
     */
    public function __construct(
        Contact $contact,
        CustomFieldValueRepository $customFieldValueRepository,
        CustomFieldRepository $customFieldRepository,
    ) {
        parent::__construct($contact, $customFieldValueRepository, $customFieldRepository);
    }
}
