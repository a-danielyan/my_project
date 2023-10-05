<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property string previous_value
 * @property string new_value
 * @property int update_id
 * @property User|null updated_by
 * @property Carbon created_at
 * @property CustomField field
 * @property string log_type
 * @property User updatedBy
 * @property ?int activity_id
 * @property Activity activity
 */
class EntityLog extends Model
{
    use HasFactory;

    public const CREATE_LOG_TYPE = 'create';
    public const EDIT_LOG_TYPE = 'edit';
    public const DELETE_LOG_TYPE = 'delete';
    public const TAG_LOG_TYPE = 'tag';

    public const SYSTEM_LOG_TYPE = 'system';
    public const MAIL_LOG_TYPE = 'mail';
    public const NOTE_LOG_TYPE = 'note';
    protected $table = 'entity_log';
    protected $fillable = [
        'entity',
        'entity_id',
        'field_id',
        'previous_value',
        'new_value',
        'updated_by',
        'update_id',
        'log_type',
        'activity_id',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'updated_by');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
