<?php

namespace App\Models;

use App\Traits\CreatedByUpdatedByTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string name
 * @property string related_entity
 * @property string remind_entity
 * @property int remind_days
 * @property string remind_type
 * @property string condition
 * @property array sender
 * @property array reminder_cc
 * @property array reminder_bcc
 * @property string subject
 * @property string reminder_text
 * @property string status
 * @property User createdBy
 * @property User updatedBy
 * @property Collection reminderLogs
 */
class Reminder extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;

    protected $table = 'reminder';

    public const REMIND_TYPE_BEFORE = 'before';
    public const REMIND_TYPE_AFTER = 'after';

    public const REMIND_ENTITY_CONTACT = 'Contact';
    public const REMIND_ENTITY_ACCOUNT = 'Account';
    public const REMIND_ENTITY_ME = 'Me';

    public const RELATED_ENTITY_SUBSCRIPTION = 'Subscription';
    public const RELATED_ENTITY_INVOICE = 'Invoice';

    protected $fillable = [
        'name',
        'related_entity',
        'remind_entity',
        'remind_days',
        'remind_type',
        'condition',
        'sender',
        'reminder_cc',
        'reminder_bcc',
        'subject',
        'reminder_text',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'sender' => 'array',
        'reminder_cc' => 'array',
        'reminder_bcc' => 'array',
    ];

    public function reminderLogs(): HasMany
    {
        return $this->hasMany(ReminderLog::class);
    }
}
