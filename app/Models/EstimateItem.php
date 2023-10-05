<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property float quantity
 * @property string description
 * @property int product_id
 * @property Product product
 * @property float discount
 * @property int parent_id
 * @property bool combine_price
 * @property float tax_percent
 */
class EstimateItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'estimate_item';

    protected $fillable = [
        'group_id',
        'product_id',
        'quantity',
        'description',
        'discount',
        'parent_id',
        'combine_price',
        'tax_percent'
    ];

    protected $casts = [
        'combine_price' => 'boolean',
    ];

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->with(['customFields']);
    }
}
