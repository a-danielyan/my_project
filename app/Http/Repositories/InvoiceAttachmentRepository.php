<?php

namespace App\Http\Repositories;

use App\Models\InvoiceAttachment;

class InvoiceAttachmentRepository extends BaseRepository
{
    /**
     * @param InvoiceAttachment $invoiceAttachment
     */
    public function __construct(
        InvoiceAttachment $invoiceAttachment,
    ) {
        $this->model = $invoiceAttachment;
    }
}
