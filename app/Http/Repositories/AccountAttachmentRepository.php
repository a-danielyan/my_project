<?php

namespace App\Http\Repositories;

use App\Models\AccountAttachment;

class AccountAttachmentRepository extends BaseRepository
{
    /**
     * @param AccountAttachment $accountAttachment
     */
    public function __construct(
        AccountAttachment $accountAttachment,
    ) {
        $this->model = $accountAttachment;
    }
}
