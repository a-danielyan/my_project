<?php

namespace App\Models\Sequence;

use App\Models\Template;
use App\Models\User;
use App\Traits\CreatedByUpdatedByTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string name
 * @property Carbon start_date
 * @property bool is_active
 * @property User createdBy
 * @property User updatedBy
 */
class Sequence extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;

    public const SEND_AFTER_UNIT_DAY = 'day';
    public const SEND_AFTER_UNIT_MONTH = 'month';

    protected $table = 'sequence';

    protected $fillable = [
        'name',
        'start_date',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
    ];

    public function templates(): BelongsToMany
    {
        return $this->belongsToMany(
            Template::class,
            'sequence_template_associations',
        );
    }

    public function templatesAssociation(): HasMany
    {
        return $this->hasMany(SequenceTemplateAssociation::class);
    }

    public function entityRelation(): HasMany
    {
        return $this->hasMany(SequenceEntityAssociation::class);
    }
}
