<?php

namespace App\Models\Sequence;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string status
 * @property Carbon sent_at
 */
class SequenceEntityLog extends Model
{
    use HasFactory;

    protected $table = 'sequence_entity_logs';
    protected $fillable = [
        'entity_id',
        'email_template_id',
        'status',
        'sent_at',
        'sequence_id'
    ];

    public const STATUS_NEW = 'new';
    public const STATUS_SENT = 'sent';
    public const STATUS_ERROR = 'error';

    protected $casts = [
        'sent_at' => 'date',
    ];
}
