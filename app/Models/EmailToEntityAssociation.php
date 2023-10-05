<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int email_id
 * @property int entity_id
 * @property string entity
 * @property Model relatedItem
 */
class EmailToEntityAssociation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'email_id',
        'entity_id',
        'entity',
    ];

    public function relatedItem(): MorphTo
    {
        return $this->morphTo('relatedItem', 'entity', 'entity_id');
    }
}
