<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Stage stage
 */
class OpportunityStageLogs extends Model
{
    use HasFactory;

    protected $table = 'opportunity_stage_log';
    protected $fillable = [
        'opportunity_id',
        'stage_id',
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }
}
