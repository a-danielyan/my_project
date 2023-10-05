<?php

namespace App\Models;

use App\Traits\CreatedByUpdatedByTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int id
 * @property string salutation
 * @property int created_by
 * @property int updated_by
 * @property string status
 * @property Collection customFields
 * @property Carbon created_at
 * @property Tag[] tag
 * @property int account_id
 * @property User|null createdBy
 * @property User|null updatedBy
 * @property Collection attachments
 * @property Account account
 * @property Carbon deleted_at
 * @property string avatar
 */
class Contact extends BaseModelWithCustomFields
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;

    protected $table = 'contact';
    public const DEFAULT_CONTACT_FIRST_NAME = 'Default';
    public const DEFAULT_CONTACT_LAST_NAME = 'Contact';

    protected $fillable = [
        'salutation',
        'created_by',
        'account_id',
        'status',
        'zoho_entity_id',
        'avatar',
    ];

    public function attachments(): HasMany
    {
        return $this->hasMany(ContactAttachments::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
