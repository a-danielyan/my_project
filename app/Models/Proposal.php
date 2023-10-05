<?php

namespace App\Models;

use App\Traits\CreatedByUpdatedByTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property-read int id
 * @property string status
 * @property string pdf_link
 * @property int template_id
 * @property Template template
 * @property int opportunity_id
 * @property Opportunity opportunity
 * @property Collection estimates
 * @property Collection estimateProposalRelation
 */
class Proposal extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;

    protected $table = 'proposal';

    public const PROPOSAL_STATUS_CREATED = 'Created';
    public const PROPOSAL_STATUS_SENT = 'Sent';
    public const PROPOSAL_STATUS_OPENED = 'Opened';
    public const PROPOSAL_STATUS_ACCEPTED = 'Accepted';

    protected $fillable = [
        'status',
        'pdf_link',
        'template_id',
        'opportunity_id',
        'created_by',
    ];

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function estimates(): BelongsToMany
    {
        return $this->belongsToMany(
            Estimate::class,
            'estimate_proposal_associations',
        )->orderByDesc('sort_order');
    }

    public function estimateProposalRelation(): HasMany
    {
        return $this->hasMany(EstimateProposalAssociation::class)->orderBy('sort_order');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
