<?php

namespace App\Http\Resource;

use App\Models\InvoiceItem;
use Illuminate\Http\Request;

/**
 * @mixin InvoiceItem
 */
class InvoiceItemResource extends InvoiceMinimalResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return
            [
                'product' => new ProductResource($this->whenLoaded('product')),
                'quantity' => $this->quantity,
                'discount' => $this->discount,
                'total' => $this->total,
                'subtotal' => $this->subtotal,
                'tax' => $this->tax,
            ];
    }
}
