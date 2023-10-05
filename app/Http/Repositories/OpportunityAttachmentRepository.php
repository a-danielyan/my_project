<?php

namespace App\Http\Repositories;

use App\Models\OpportunityAttachment;

class OpportunityAttachmentRepository extends BaseRepository
{
    /**
     * @param OpportunityAttachment $opportunityAttachment
     */
    public function __construct(
        OpportunityAttachment $opportunityAttachment,
    ) {
        $this->model = $opportunityAttachment;
    }
}
