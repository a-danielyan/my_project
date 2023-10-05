<?php

namespace App\Models;

use App\Models\Interfaces\FilteredInterface;
use App\Traits\FilterScopeTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int id
 * @property string email_id
 * @property int token_id
 * @property Carbon received_date
 * @property string from
 * @property string to
 * @property string subject
 * @property string content
 * @property OauthToken token
 * @property EmailToEntityAssociation relatedEmailAssociation
 * @property string status
 * @property array schedule_details
 * @property string error
 */
class Email extends Model implements FilteredInterface
{
    use HasFactory;
    use FilterScopeTrait;

    protected $table = 'email';
    protected $fillable = [
        'email_id',
        'token_id',
        'received_date',
        'from',
        'to',
        'subject',
        'content',
        'status',
        'send_at',
        'schedule_details',
    ];

    protected $casts = [
        'to' => 'array',
        'schedule_details' => 'array',
        'send_at' => 'date',
    ];

    public const STATUS_SENT = 'Sent';
    public const STATUS_DELIVERED = 'Delivered';
    public const STATUS_COMPLAINED = 'Complained';
    public const STATUS_OPENED = 'Opened';
    public const STATUS_CLICKED = 'Clicked';
    public const STATUS_SCHEDULED = 'Scheduled';
    public const STATUS_ERROR = 'Error';
    public const STATUS_NEW = 'New';
    public const STATUS_FAILED = 'Failed';
    public const STATUS_UNSUBSCRIBED = 'Unsubscribed';

    public static function filterArray(): array
    {
        return [
            'equal' => [
                'id',
                'token_id',
                'received_date',
                'from',
                'to',
                'subject',
            ],
            'like' => [],
            'sort' => [
                'id',
                'received_date',
                'from',
                'to',
                'subject',
            ],
            'relation' => [],
            'custom' => [
                'relatedToEntity',
                'relatedToId',
            ],
            'custom_sort' => [],
        ];
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(OauthToken::class);
    }

    public function relatedEmailAssociation(): HasOne
    {
        return $this->hasOne(EmailToEntityAssociation::class);
    }

    protected function filterByRelatedToEntity(Builder $query, string $value): Builder
    {
        $query->whereHas('relatedEmailAssociation', function ($query) use ($value) {
            $query->where('entity', 'App\Models\\' . $value);
        });

        return $query;
    }

    protected function filterByRelatedToId(Builder $query, string $value): Builder
    {
        $query->whereHas('relatedEmailAssociation', function ($query) use ($value) {
            $query->where('entity_id', $value);
        });

        return $query;
    }
}
