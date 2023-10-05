<?php

namespace App\Models;

use App\Models\Interfaces\FilteredInterface;
use App\Traits\FilterScopeTrait;
use App\Traits\TagAssociationTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property User related_to
 * @property Carbon started_at
 * @property Carbon ended_at
 * @property string activity_type
 * @property string activity_status
 * @property string priority
 * @property Carbon due_date
 * @property string subject
 * @property string related_to_entity
 * @property int related_to_id
 * @property string description
 * @property User created_by
 * @property User|null updated_by
 * @property Collection reminders
 * @property User|null relatedUser
 * @property User|null createdBy
 * @property User|null updatedBy
 * @property Collection tag
 * @property Model relatedItem
 */
class Activity extends Model implements FilteredInterface
{
    use HasFactory;
    use SoftDeletes;
    use TagAssociationTrait;
    use FilterScopeTrait;


    protected $table = 'activity';
    protected $fillable = [
        'related_to',
        'started_at',
        'ended_at',
        'activity_type',
        'activity_status',
        'priority',
        'due_date',
        'subject',
        'related_to_entity',
        'related_to_id',
        'description',
        'created_by',
        'updated_by',
        'reminder_at',
        'reminder_type',
        'status',
        'zoho_entity_id',
    ];

    public const ACTIVITY_TYPE_TASK = 'Task';
    public const ACTIVITY_TYPE_MEETING = 'Meeting';
    public const ACTIVITY_TYPE_INTERNAL_NOTE = 'InternalNote';
    public const ACTIVITY_TYPE_CALL = 'Call';
    public const ACTIVITY_TYPE_EMAIL = 'Email';
    public const ACTIVITY_TYPE_DEADLINE = 'Deadline';

    public const ACTIVITY_TYPES = [
        self::ACTIVITY_TYPE_TASK,
        self::ACTIVITY_TYPE_MEETING,
        self::ACTIVITY_TYPE_INTERNAL_NOTE,
        self::ACTIVITY_TYPE_CALL,
    ];
    public const ACTIVITY_STATUS_NOT_STARTED = 'Not started';
    public const ACTIVITY_STATUS_DEFERRED = 'Deferred';
    public const ACTIVITY_STATUS_IN_PROGRESS = 'In Progress';
    public const ACTIVITY_STATUS_COMPLETED = 'Completed';
    public const ACTIVITY_STATUS_WAITING_FOR_INPUT = 'Waiting for input';
    public const ACTIVITY_STATUS_OVERDUE = 'Overdue';
    public const ACTIVITY_STATUS_CANCELLED = 'Cancelled';

    public const ACTIVITY_STATUSES = [
        self::ACTIVITY_STATUS_NOT_STARTED,
        self::ACTIVITY_STATUS_DEFERRED,
        self::ACTIVITY_STATUS_IN_PROGRESS,
        self::ACTIVITY_STATUS_COMPLETED,
        self::ACTIVITY_STATUS_WAITING_FOR_INPUT,
        self::ACTIVITY_STATUS_OVERDUE,
        self::ACTIVITY_STATUS_CANCELLED,
    ];
    public const PRIORITY_LOWEST_STATUS = 'Lowest';
    public const PRIORITY_LOW_STATUS = 'Low';
    public const PRIORITY_NORMAL_STATUS = 'Normal';
    public const PRIORITY_HIGH_STATUS = 'High';
    public const PRIORITY_HIGHEST_STATUS = 'Highest';

    public const PRIORITY_STATUSES = [
        self::PRIORITY_LOWEST_STATUS,
        self::PRIORITY_LOW_STATUS,
        self::PRIORITY_NORMAL_STATUS,
        self::PRIORITY_HIGH_STATUS,
        self::PRIORITY_HIGHEST_STATUS,
    ];

    public function relatedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'related_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'updated_by');
    }

    public static function filterArray(): array
    {
        return [
            'equal' => [
                'id',
                'activity_type',
                'activity_status',
                'priority',
                'due_date',
                'subject',
                'description',
                'related_to',
                'related_to_id',
            ],
            'like' => [
            ],
            'sort' => [
                'activity_type',
                'activity_status',
                'priority',
                'due_date',
                'subject',
                'description',
                'related_to',
                'started_at',
                'ended_at',
            ],
            'relation' => [
                'tag' => [
                    'eloquent_m' => 'tagsAssociation.masterTagsData',
                    'where' => 'tag',
                ],
            ],
            'custom' => [
                'activityStatus',
                'status',
                'relatedToEntity',
                'beforeDate',
                'afterDate',
            ],
            'custom_sort' => [],
        ];
    }

    protected function filterByStatus(Builder $query, array $value): Builder
    {
        $query->where(function ($query) use ($value) {
            foreach ($value as $status) {
                $query->orWhere('status', $status);
            }
        });

        return $query;
    }

    protected function filterByRelatedToEntity(Builder $query, string $value): Builder
    {
        $query->where('related_to_entity', 'App\Models\\' . $value);

        return $query;
    }

    protected function filterByAfterDate(Builder $query, string $value): Builder
    {
        $query->where('due_date', '>=', $value);

        return $query;
    }

    protected function filterByBeforeDate(Builder $query, string $value): Builder
    {
        $query->where('due_date', '<=', $value);

        return $query;
    }


    public function reminders(): HasMany
    {
        return $this->hasMany(ActivityReminder::class);
    }

    public function relatedItem(): MorphTo
    {
        return $this->morphTo('relatedItem', 'related_to_entity', 'related_to_id');
    }

    public function zohoRelationSyncStatus(): MorphMany
    {
        return $this->morphMany(FixZohoRelationStatusSync::class, 'entity');
    }
}
