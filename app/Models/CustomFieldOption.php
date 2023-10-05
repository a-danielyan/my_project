<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property string name
 * @property string sort_order
 */
class CustomFieldOption extends Model
{
    use HasFactory;

    protected $table = 'custom_field_option';
    protected $fillable = [
        'name',
        'sort_order',
        'custom_field_id',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }
}
