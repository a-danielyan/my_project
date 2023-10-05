<?php

namespace App\Models;

use App\Traits\CreatedByUpdatedByTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int id
 * @property int created_by
 * @property int updated_by
 * @property string status
 * @property Collection customFields
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Tag[] tag
 * @property User|null createdBy
 * @property User|null updatedBy
 * @property Collection accountsPayable
 * @property Lead lead
 * @property Collection attachments
 * @property Carbon deleted_at
 * @property Collection contacts
 * @property string avatar
 * @property Collection opportunities
 * @property string stripe_customer_id
 * @property Activity internalNote
 */
class Account extends BaseModelWithCustomFields
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;

    protected $table = 'account';
    public const EXEMPT_TAX_STATUS = 'Exempt';
    public const DEFAULT_ACCOUNT_NAME = 'Default Account';

    protected $fillable = [
        'cms_client_id',
        'parent_account_id',
        'created_by',
        'lead_id',
        'status',
        'zoho_entity_id',
        'avatar',
        'stripe_customer_id',
    ];

    public function accountsPayable(): BelongsToMany
    {
        return $this->belongsToMany(
            Contact::class,
            AccountPayableContacts::class,
            'account_id',
            'contact_id',
        );
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AccountAttachment::class);
    }

    public static function filterArray(): array
    {
        return [
            'equal' => [
                'id',
            ],
            'like' => [
            ],
            'sort' => [
                'id',
            ],
            'relation' => [
                'tag' => [
                    'eloquent_m' => 'tagsAssociation.masterTagsData',
                    'where' => 'tag',
                ],
            ],
            'custom' => [
                'status',
            ],
            'custom_sort' => [],
        ];
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function internalNote(): HasOne
    {
        return $this->hasOne(Activity::class, 'related_to_id')
            ->where('related_to_entity', self::class)
            ->where('activity_type', Activity::ACTIVITY_TYPE_INTERNAL_NOTE);
    }
}
