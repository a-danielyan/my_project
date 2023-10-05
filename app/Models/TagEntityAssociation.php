<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TagEntityAssociation extends Model
{
    use HasFactory;

    protected $table = 'tag_entity_association';

    protected $fillable = [
        'tag_id',
        'entity',
        'entity_id',
    ];


    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function masterTagsData(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }
}
