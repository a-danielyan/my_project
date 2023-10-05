<?php

namespace App\Models;

use App\Traits\CreatedByUpdatedByTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property int id
 * @property int created_by
 * @property int updated_by
 * @property string status
 * @property Collection customFields
 * @property Carbon created_at
 * @property Tag[] tag
 * @property User|null createdBy
 * @property User|null updatedBy
 * @property Carbon deleted_at
 * @property Collection attachments
 * @property string stripe_product_id
 */
class Product extends BaseModelWithCustomFields
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;

    protected $table = 'product';

    protected $fillable = [
        'created_by',
        'status',
        'stripe_product_id',
        'zoho_entity_id',
    ];

    public function attachments(): HasMany
    {
        return $this->hasMany(ProductAttachment::class);
    }
}
