<?php

namespace App\Http\Repositories;

use App\Models\ContactAttachments;

class ContactAttachmentRepository extends BaseRepository
{
    /**
     * @param ContactAttachments $contactAttachments
     */
    public function __construct(
        ContactAttachments $contactAttachments,
    ) {
        $this->model = $contactAttachments;
    }
}
