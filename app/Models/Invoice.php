<?php

namespace App\Models;

use App\Models\Interfaces\FilteredInterface;
use App\Models\Interfaces\ModelWithContactInterface;
use App\Traits\FilterScopeTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property int opportunity_id
 * @property Carbon created_at
 * @property float sub_total
 * @property float total_tax
 * @property float total_discount
 * @property float grand_total
 * @property int contact_id
 * @property string payment_term
 * @property string terms_and_conditions
 * @property Carbon|null due_date
 * @property string status
 * @property string filename
 * @property int|null updated_by
 * @property Estimate|null estimate
 * @property Opportunity|null opportunity
 * @property Contact|null client
 * @property Account|null account
 * @property Contact|null contact
 * @property Collection invoiceItem
 * @property Collection invoicePayments
 * @property string invoice_number
 * @property string notes
 * @property User invoiceOwner
 * @property int owner_id
 * @property string order_type
 * @property Carbon ship_date
 * @property string ship_carrier
 * @property string ship_instruction
 * @property string track_code_standard
 * @property string track_code_special
 * @property float balance_due
 * @property float ship_cost
 * @property int sql_to_order_duration
 * @property string cancel_reason
 * @property string cancel_details
 * @property User canceledBy
 * @property float refund_amount
 * @property Carbon refund_date
 * @property string refund_reason
 * @property User refundedBy
 * @property Collection attachments
 * @property string stripe_invoice_id
 * @property ?int zoho_entity_id_sales_order
 * @property ?int zoho_entity_id_invoice
 * @property Carbon paid_at
 * @property Carbon order_date
 * @property double tax_percent
 * @property double discount_percent
 * @property Collection statusLog
 * @property Carbon terms_accepted_at
 * @property Carbon opened_at
 */
class Invoice extends BaseModelWithCustomFields implements FilteredInterface, ModelWithContactInterface
{
    use HasFactory;
    use SoftDeletes;
    use FilterScopeTrait;

    public const STATUS_DISABLED = 'Disabled';

    protected $table = 'invoice';

    protected $fillable = [
        'opportunity_id',
        'estimate_id',
        'account_id',
        'contact_id',
        'sub_total',
        'total_tax',
        'total_discount',
        'grand_total',
        'payment_term',
        'due_date',
        'terms_and_conditions',
        'status',
        'updated_by',
        'client_po',
        'parent_po',
        'previous_po',
        'notes',
        'owner_id',
        'order_type',
        'ship_date',
        'ship_carrier',
        'ship_instruction',
        'track_code_standard',
        'track_code_special',
        'ship_cost',
        'cancel_reason',
        'cancel_details',
        'canceled_by',
        'refund_amount',
        'refund_date',
        'refund_reason',
        'refunded_by',
        'balance_due',
        'stripe_invoice_id',
        'zoho_entity_id_sales_order',
        'zoho_entity_id_invoice',
        'paid_at',
        'order_date',
        'tax_percent',
        'discount_percent',
        'terms_accepted_at',
        'opened_at',
    ];

    public const PAYMENT_TERM_PREPAID = 'Net 0';
    public const AVAILABLE_PAYMENT_TERMS = [
        self::PAYMENT_TERM_PREPAID,
        'Net 1',
        'Net 7',
        'Net 14',
        'Net 15',
        'Net 30',
        'Net 45',
        'Net 60',
        'Net 90',
    ];
    public const INVOICE_STATUS_SENT = 'Sent';
    public const INVOICE_STATUS_PAYMENT_PENDING = 'Payment Pending';
    public const INVOICE_STATUS_DRAFT = 'Draft';
    public const INVOICE_STATUS_PAYMENT_COMPLETED = 'Payment Completed';
    public const INVOICE_STATUS_PARTIALLY_PAID = 'Partially Paid';
    public const INVOICE_STATUS_TERMS_ACCEPTED = 'Terms Accepted';

    public const INVOICE_STATUS_APPROVED_FOR_FULLFILMENT = 'Approved for Fullfilment';
    public const INVOICE_STATUS_CANCELLED = 'Cancelled / Voided';
    public const INVOICE_STATUS_CLOSED = 'Closed';
    public const INVOICE_STATUS_CROSS_SHIP = 'Cross Ship';
    public const INVOICE_STATUS_OPEN = 'Open';
    public const INVOICE_STATUS_SHIPPED_NOT_PAID = 'Shipped (Not Paid)';
    public const INVOICE_STATUS_TECH_REFRESH = 'Tech Refresh';

    protected $casts = [
        'paid_at' => 'date',
        'order_date' => 'date',
        'terms_accepted_at' => 'date',
        'opened_at' => 'date',
    ];
    public const AVAILABLE_INVOICE_STATUSES = [
        self::INVOICE_STATUS_DRAFT,
        self::INVOICE_STATUS_SENT,
        self::INVOICE_STATUS_PAYMENT_PENDING,
        self::INVOICE_STATUS_PAYMENT_COMPLETED,
        self::INVOICE_STATUS_PARTIALLY_PAID,
        self::INVOICE_STATUS_TERMS_ACCEPTED,
        self::INVOICE_STATUS_APPROVED_FOR_FULLFILMENT,
        self::INVOICE_STATUS_CANCELLED,
        self::INVOICE_STATUS_CLOSED,
        self::INVOICE_STATUS_CROSS_SHIP,
        self::INVOICE_STATUS_OPEN,
        self::INVOICE_STATUS_SHIPPED_NOT_PAID,
        self::INVOICE_STATUS_TECH_REFRESH,
    ];


    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function invoiceItemGroup(): HasMany
    {
        return $this->hasMany(InvoiceShippingGroupItem::class);
    }

    public function invoicePayments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoiceOwner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }

    public function canceledBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'canceled_by');
    }

    public function refundedBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'refunded_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(InvoiceAttachment::class);
    }

    public static function filterArray(): array
    {
        return [
            'equal' => [
                'id',
                'opportunityId',
                'estimateId',
                'accountId',
                'contactId',
                'paymentTerm',
                'dueDate',
                'status',
                'invoiceNumber',
                'clientPo',
                'parentPo',
                'previousPo',
                'paymentId',
                'ownerId',
                'orderType',
                'shipDate',
                'shipCarrier',
                'SQLToOrderDuration',
            ],
            'like' => [
            ],
            'sort' => [
                'id',
                'opportunityId',
                'estimateId',
                'accountId',
                'contactId',
                'subTotal',
                'totalTax',
                'totalDiscount',
                'grandTotal',
                'paymentTerm',
                'dueDate',
                'status',
                'orderType',
                'shipDate',
                'shipCarrier',
                'SQLToOrderDuration',
            ],
            'relation' => [
                'tag' => [
                    'eloquent_m' => 'tagsAssociation.masterTagsData',
                    'where' => 'tag',
                ],
            ],
            'custom' => ['product'],
            'custom_sort' => [],
        ];
    }

    protected function filterByProduct(Builder $query, string $value): Builder
    {
        $query->whereHas('invoiceItem', function ($query) use ($value) {
            $query->where('product_id', $value);
        });

        return $query;
    }

    public function statusLog(): HasMany
    {
        return $this->hasMany(InvoiceStatusLog::class);
    }
}
