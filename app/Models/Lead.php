<?php

namespace App\Models;

use App\Models\Interfaces\FilteredInterface;
use App\Traits\CreatedByUpdatedByTrait;
use App\Traits\FilterScopeTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property int id
 * @property string salutation
 * @property int created_by
 * @property int updated_by
 * @property string status
 * @property string avatar
 * @property Collection customFields
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Tag[] tag
 * @property User|null createdBy
 * @property User|null updatedBy
 * @property Carbon deleted_at
 * @property Carbon apollo_synced_at
 * @property Collection activity
 * @property Collection emailAssociation
 * @property Collection timeline
 * @property Collection attachments
 * @property Activity internalNote
 */
class Lead extends BaseModelWithCustomFields implements FilteredInterface
{
    use HasFactory;
    use SoftDeletes;
    use FilterScopeTrait;
    use CreatedByUpdatedByTrait;

    protected $table = 'lead';

    protected $fillable = [
        'salutation',
        'status',
        'created_by',
        'zoho_entity_id',
        'avatar',
    ];

    public const AVAILABLE_SALUTATION = ['Mr.', 'Ms.', 'Dr.'];

    public function attachments(): HasMany
    {
        return $this->hasMany(LeadAttachments::class);
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

    public function activity(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activity', 'related_to_entity', 'related_to_id')
            ->where('activity_type', '!=', Activity::ACTIVITY_TYPE_INTERNAL_NOTE);
    }

    public function internalNote(): HasOne
    {
        return $this->hasOne(Activity::class, 'related_to_id')
            ->where('related_to_entity', self::class)
            ->where('activity_type', Activity::ACTIVITY_TYPE_INTERNAL_NOTE);
    }

    public function emailAssociation(): MorphMany
    {
        return $this->morphMany(EmailToEntityAssociation::class, 'email', 'entity', 'entity_id');
    }

    public function timeline(): MorphMany
    {
        return $this->morphMany(EntityLog::class, 'timeline', 'entity', 'entity_id');
    }
}
