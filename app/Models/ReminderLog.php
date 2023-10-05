<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderLog extends Model
{
    use HasFactory;

    protected $table = 'reminder_log';

    protected $fillable = [
        'reminder_id',
        'sent_entity',
        'sent_entity_id',
        'reminder_date',
        'status',
        'error',
    ];

    public const STATUS_DONE = 'done';
    public const STATUS_ERROR = 'error';
}
