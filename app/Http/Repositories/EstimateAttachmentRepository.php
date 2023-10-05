<?php

namespace App\Http\Repositories;

use App\Models\EstimateAttachment;

class EstimateAttachmentRepository extends BaseRepository
{
    /**
     * @param EstimateAttachment $estimateAttachment
     */
    public function __construct(
        EstimateAttachment $estimateAttachment,
    ) {
        $this->model = $estimateAttachment;
    }
}
