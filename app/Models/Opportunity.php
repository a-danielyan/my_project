<?php

namespace App\Models;

use App\Traits\CreatedByUpdatedByTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property int id
 * @property int created_by
 * @property int updated_by
 * @property string opportunity_name
 * @property string project_type
 * @property int stage_id
 * @property Carbon|null expecting_closing_date
 * @property float expected_revenue
 * @property User|null createdBy
 * @property User|null updatedBy
 * @property Collection customFields
 * @property Carbon created_at
 * @property Tag[] tag
 * @property Collection stageLog
 * @property Stage stage
 * @property Collection estimates
 * @property Account account
 * @property int estimates_count
 * @property Carbon closed_at
 * @property int account_id
 * @property Activity internalNote
 * @property Proposal proposal
 */
class Opportunity extends BaseModelWithCustomFields
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;

    protected $table = 'opportunity';

    public const EXISTED_BUSINESS = 'Existing business';
    public const NEW_BUSINESS = 'New business';

    protected $fillable = [
        'created_by',
        'updated_by',
        'opportunity_name',
        'project_type',
        'expecting_closing_date',
        'stage_id',
        'expected_revenue',
        'status',
        'zoho_entity_id',
        'account_id',
        'estimates_count',
        'closed_at',
    ];

    public function stageLog(): HasMany
    {
        return $this->hasMany(OpportunityStageLogs::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function estimates(): HasMany
    {
        return $this->hasMany(Estimate::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(OpportunityAttachment::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function internalNote(): HasOne
    {
        return $this->hasOne(Activity::class, 'related_to_id')
            ->where('related_to_entity', self::class)
            ->where('activity_type', Activity::ACTIVITY_TYPE_INTERNAL_NOTE);
    }

    public function proposal(): HasOne
    {
        return $this->hasOne(Proposal::class);
    }
}
