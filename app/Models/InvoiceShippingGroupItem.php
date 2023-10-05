<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array address
 * @property int id
 */
class InvoiceShippingGroupItem extends Model
{
    use HasFactory;

    protected $table = 'invoice_shipping_group_item';

    protected $fillable = [
        'contact_id',
        'invoice_id',
        'address',
    ];

    protected $casts = [
        'address' => 'array',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'group_id');
    }
}
