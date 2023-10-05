<?php

namespace App\Http\Resource;

use App\Models\InvoiceStatusLog;
use Illuminate\Http\Request;

/**
 * @mixin InvoiceStatusLog
 */
class InvoiceStatusLogResource extends BaseResourceWithCustomField
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status,
            'createdAt' => $this->created_at,
        ];
    }
}
