<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateProposalAssociation extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_id',
        'proposal_id',
    ];
}
