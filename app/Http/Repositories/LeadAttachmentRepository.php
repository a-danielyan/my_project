<?php

namespace App\Http\Repositories;

use App\Models\LeadAttachments;

class LeadAttachmentRepository extends BaseRepository
{
    /**
     * @param LeadAttachments $leadAttachments
     */
    public function __construct(
        LeadAttachments $leadAttachments,
    ) {
        $this->model = $leadAttachments;
    }
}
