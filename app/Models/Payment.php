<?php

namespace App\Models;

use App\Models\Interfaces\FilteredInterface;
use App\Traits\FilterScopeTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property int account_id
 * @property string payment_name
 * @property int invoice_id
 * @property float payment_received
 * @property string payment_method
 * @property string payment_source
 * @property string credit_card_type
 * @property string payment_processor
 * @property Carbon payment_date
 * @property string notes
 * @property int received_by
 * @property int refund_invoice
 * @property Account account
 * @property Invoice invoice
 * @property User receivedBy
 * @property Invoice|null refundInvoice
 */
class Payment extends Model implements FilteredInterface
{
    use HasFactory;
    use FilterScopeTrait;

    protected $table = 'payment';

    public const PAYMENT_METHOD_CREDIT_CARD = 'Credit Card';
    public const PAYMENT_METHOD_ACH = 'ACH';
    public const PAYMENT_METHOD_CHECK = 'Check';
    public const PAYMENT_METHOD_CASH = 'Cash';
    public const PAYMENT_METHOD_WIRE = 'Wire';
    public const PAYMENT_METHOD_OTHER = 'Other';

    public const AVAILABLE_PAYMENT_METHODS = [
        self::PAYMENT_METHOD_CREDIT_CARD,
        self::PAYMENT_METHOD_CHECK,
        self::PAYMENT_METHOD_ACH,
        self::PAYMENT_METHOD_WIRE,
        self::PAYMENT_METHOD_CASH,
        self::PAYMENT_METHOD_OTHER,
    ];


    public const CREDIT_CARD_TYPE_AMEX = 'AMEX';
    public const CREDIT_CARD_TYPE_VISA = 'VISA';
    public const CREDIT_CARD_TYPE_MASTERCARD = 'MASTERCARD';
    public const CREDIT_CARD_TYPE_DISCOVER = 'DISCOVER';

    public const AVAILABLE_CREDIT_CARDS = [
        self::CREDIT_CARD_TYPE_AMEX,
        self::CREDIT_CARD_TYPE_VISA,
        self::CREDIT_CARD_TYPE_MASTERCARD,
        self::CREDIT_CARD_TYPE_DISCOVER,
    ];

    public const PAYMENT_PROCESSOR_STRIPE = 'STRIPE';
    public const PAYMENT_PROCESSOR_AUTHORIZE = 'AUTHORIZE';
    public const PAYMENT_PROCESSOR_PAYPAL = 'PAYPAL';

    public const AVAILABLE_PAYMENT_PROCESSORS = [
        self::PAYMENT_PROCESSOR_STRIPE,
        self::PAYMENT_PROCESSOR_AUTHORIZE,
        self::PAYMENT_PROCESSOR_PAYPAL,
    ];


    protected $fillable = [
        'account_id',
        'payment_name',
        'invoice_id',
        'payment_received',
        'payment_method',
        'payment_source',
        'payment_date',
        'notes',
        'received_by',
        'refund_invoice',
        'payment_processor',
        'credit_card_type',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function refundInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'refund_invoice');
    }

    public static function filterArray(): array
    {
        return [
            'equal' => [
                'id',
                'accountId',
                'paymentName',
                'invoiceId',
                'paymentReceived',
                'paymentMethod',
                'paymentDate',
                'notes',
                'receivedBy',
                'refundInvoice',
            ],
            'like' => [],
            'sort' => [
                'id',
                'accountId',
                'paymentName',
                'invoiceId',
                'paymentReceived',
                'paymentMethod',
                'paymentDate',
                'notes',
                'receivedBy',
                'refundInvoice',
            ],
            'relation' => [],
            'custom' => [],
            'custom_sort' => [],
        ];
    }
}
