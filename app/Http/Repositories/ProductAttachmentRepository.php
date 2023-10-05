<?php

namespace App\Http\Repositories;

use App\Models\ProductAttachment;

class ProductAttachmentRepository extends BaseRepository
{
    /**
     * @param ProductAttachment $productAttachment
     */
    public function __construct(
        ProductAttachment $productAttachment,
    ) {
        $this->model = $productAttachment;
    }
}
