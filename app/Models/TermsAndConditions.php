<?php

namespace App\Models;

use App\Traits\CreatedByUpdatedByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string entity
 * @property string terms_and_condition
 * @property int id
 */
class TermsAndConditions extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;

    public const ESTIMATE_ENTITY = 'Estimate';
    public const INVOICE_ENTITY = 'Invoice';
    public const PROPOSAL_ENTITY = 'Proposal';

    protected $fillable = [
        'terms_and_condition',
        'entity',
        'created_by',
        'updated_by',
    ];
}
