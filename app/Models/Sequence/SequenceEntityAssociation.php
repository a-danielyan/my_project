<?php

namespace App\Models\Sequence;

use App\Models\BaseModelWithCustomFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int id
 * @property int count_emails_sent
 * @property string entity_type
 * @property int entity_id
 * @property BaseModelWithCustomFields entity
 * @property int sequence_id
 */
class SequenceEntityAssociation extends Model
{
    use HasFactory;

    protected $table = 'sequence_entity_associations';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'count_emails_sent',
        'sequence_id',
    ];

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(Sequence::class);
    }

    public function logRecord(): HasMany
    {
        return $this->hasMany(SequenceEntityLog::class, 'entity_id');
    }
}
