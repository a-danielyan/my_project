<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property string reminder_type
 * @property int reminder_time
 * @property string reminder_unit
 */
class ActivityReminder extends Model
{
    use HasFactory;

    protected $table = 'activity_reminder';
    protected $fillable = [
        'activity_id',
        'reminder_type',
        'reminder_time',
        'reminder_at_unit',
    ];
    public $timestamps = false;
    public const REMINDER_TYPE_EMAIL = 'email';
    public const REMINDER_TYPE_NOTIFICATION = 'notification';

    public const REMINDER_UNIT_MINUTES = 'minutes';
    public const REMINDER_UNIT_HOURS = 'hours';
    public const REMINDER_UNIT_DAYS = 'days';
    public const REMINDER_UNIT_MONTH = 'month';

    public const AVAILABLE_REMINDER_UNITS = [
        self::REMINDER_UNIT_MINUTES,
        self::REMINDER_UNIT_HOURS,
        self::REMINDER_UNIT_DAYS,
        self::REMINDER_UNIT_MONTH,
    ];

    public const AVAILABLE_REMINDER_TYPE = [
        self::REMINDER_TYPE_EMAIL,
        self::REMINDER_TYPE_NOTIFICATION,
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
