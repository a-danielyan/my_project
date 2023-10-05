<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int id
 * @property array address
 */
class EstimateShippingGroupItems extends Model
{
    use HasFactory;

    protected $table = 'estimate_shipping_group_item';

    protected $fillable = [
        'contact_id',
        'estimate_id',
        'address',
    ];

    protected $casts = [
        'address' => 'array',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(EstimateItem::class, 'group_id');
    }
}
