<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int solution_set_id
 * @property int product_id
 * @property float quantity
 * @property float price
 * @property string description
 * @property float discount
 * @property Product product
 */
class SolutionSetItems extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'solution_set_item';

    protected $fillable = [
        'solution_set_id',
        'product_id',
        'quantity',
        'price',
        'description',
        'discount',

    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
