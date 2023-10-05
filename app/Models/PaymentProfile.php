<?php

namespace App\Models;

use App\Models\Interfaces\FilteredInterface;
use App\Traits\CreatedByUpdatedByTrait;
use App\Traits\FilterScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property Account account
 * @property string payment_name
 * @property string payment_method
 * @property string billing_street_address
 * @property string billing_city
 * @property string billing_state
 * @property string billing_postal_code
 * @property string billing_country
 * @property User createdBy
 */
class PaymentProfile extends Model implements FilteredInterface
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;
    use FilterScopeTrait;

    protected $table = 'payment_profile';

    protected $fillable = [
        'account_id',
        'payment_name',
        'payment_method',
        'billing_street_address',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
        'created_by',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public static function filterArray(): array
    {
        return [
            'equal' => [
                'id',
                'account_id',
            ],
            'like' => [],
            'sort' => [
                'id',
                'account_id',
            ],
            'relation' => [],
            'custom' => [],
            'custom_sort' => [],
        ];
    }
}
