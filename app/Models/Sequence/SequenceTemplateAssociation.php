<?php

namespace App\Models\Sequence;

use App\Models\Template;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Template template
 * @property int send_after
 * @property int template_id
 * @property int sequence_id
 * @property string send_after_unit
 */
class SequenceTemplateAssociation extends Model
{
    use HasFactory;

    protected $table = 'sequence_template_associations';

    protected $fillable = [
        'sequence_id',
        'template_id',
        'send_after',
        'send_after_unit',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
