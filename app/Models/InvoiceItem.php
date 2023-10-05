<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property float quantity
 * @property float price
 * @property float total
 * @property float subtotal
 * @property float discount
 * @property float tax
 */
class InvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'invoice_item';

    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity',
        'group_id',
        'discount',
        'total',
        'subtotal',
        'tax',
        'tax_percent',
    ];

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->with(['customFields']);
    }
}
