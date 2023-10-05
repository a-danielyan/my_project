<?php

namespace App\Models;

use App\Models\Interfaces\ModelWithContactInterface;
use App\Traits\CreatedByUpdatedByTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property int created_by
 * @property int updated_by
 * @property string status
 * @property \Illuminate\Support\Collection customFields
 * @property Carbon created_at
 * @property string|null estimate_name
 * @property Carbon|null estimate_date
 * @property Carbon|null estimate_validity_duration
 * @property Tag[] tag
 * @property Collection estimateItemGroup
 * @property Opportunity opportunity
 * @property Account account
 * @property Contact contact
 * @property User|null createdBy
 * @property User|null updatedBy
 * @property string estimate_number
 * @property float sub_total
 * @property float total_tax
 * @property float total_discount
 * @property float grand_total
 * @property int estimate_number_for_opportunity
 * @property Invoice invoice
 * @property Collection attachments
 * @property string stripe_quote_id
 * @property float discount_percent
 * @property float tax_percent
 * @property int opportunity_id
 */
class Estimate extends BaseModelWithCustomFields implements ModelWithContactInterface
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;

    protected $table = 'estimate';

    protected $casts = [
        'estimate_validity_duration' => 'date',
    ];
    protected $fillable = [
        'created_by',
        'status',
        'estimate_name',
        'estimate_date',
        'estimate_validity_duration',
        'opportunity_id',
        'account_id',
        'contact_id',
        'zoho_entity_id',
        'estimate_number_for_opportunity',
        'stripe_quote_id',
        'tax_percent',
        'discount_percent',
    ];
    public const ESTIMATE_STATUS_DRAFT = 'Draft';
    public const ESTIMATE_STATUS_SENT = 'Sent';
    public const ESTIMATE_STATUS_ACCEPTED = 'Accepted';
    public const STATUS_DELIVERED = 'Delivered';
    public const STATUS_REQUEST_CHANGES = 'Requested Changes';
    public const STATUS_REJECTED = 'Rejected';
    public const STATUS_CANCELLED = 'Canceled/Voided';
    public const STATUS_CLOSED_WON = 'Closed Won';
    public const STATUS_CLOSED_LOST = 'Closed Lost';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_DENIED = 'Denied';
    public const STATUS_IN_REVIEW = 'In Review';
    public const STATUS_PRESENTED = 'Presented';
    public const STATUS_NEEDS_REVIEW = 'Needs Review';
    public const STATUS_INVOICED = 'invoiced';
    public const STATUS_CONFIRMED = 'Confirmed';


    public const AVAILABLE_STATUSES = [
        self::ESTIMATE_STATUS_DRAFT,
        self::ESTIMATE_STATUS_SENT,
        self::STATUS_DELIVERED,
        self::ESTIMATE_STATUS_ACCEPTED,
        self::STATUS_REQUEST_CHANGES,
        self::STATUS_REJECTED,
        self::STATUS_CANCELLED,
        self::STATUS_CLOSED_WON,
        self::STATUS_APPROVED,
        self::STATUS_DENIED,
        self::STATUS_IN_REVIEW,
        self::STATUS_PRESENTED,
        self::STATUS_CLOSED_LOST,
        self::STATUS_NEEDS_REVIEW,
        self::STATUS_INVOICED,
        self::STATUS_CONFIRMED,
    ];

    public function estimateItemGroup(): HasMany
    {
        return $this->hasMany(EstimateShippingGroupItems::class);
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class, 'opportunity_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(EstimateAttachment::class);
    }

    public function zohoRelationSyncStatus(): MorphMany
    {
        return $this->morphMany(FixZohoRelationStatusSync::class, 'entity');
    }
}
