<?php

namespace App\Models;

use App\Traits\CreatedByUpdatedByTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property string subscription_name
 * @property User owner
 * @property Account account
 * @property Invoice invoice
 * @property Contact contact
 * @property string parent_po
 * @property string previous_po
 * @property string order_z_number
 * @property Carbon ended_at
 * @property User createdBy
 * @property User|null updatedBy
 * @property Collection attachments
 * @property Collection devices
 * @property Collection items
 */
class Subscription extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;

    protected $table = 'subscription';

    protected $fillable = [
        'created_by',
        'subscription_name',
        'owner_id',
        'account_id',
        'invoice_id',
        'contact_id',
        'ended_at',
        'stripe_subscription_id',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SubscriptionAttachments::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(SubscriptionDevices::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SubscriptionItems::class);
    }
}
