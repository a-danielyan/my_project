<?php

namespace App\Models;

use App\Traits\CreatedByUpdatedByTrait;
use App\Traits\TagAssociationTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Collection customFields
 */
class BaseModelWithCustomFields extends Model
{
    use TagAssociationTrait;
    use CreatedByUpdatedByTrait;

    public const STATUS_DISABLED = 'Disabled';
    public function customFields(): MorphMany
    {
        return $this->morphMany(CustomFieldValues::class, 'customFields', 'entity', 'entity_id');
    }

    public function customFieldValues(): HasMany
    {
        return $this->hasMany(CustomFieldValues::class, 'entity_id', 'id')
            ->where('entity', static::class);
    }
}
