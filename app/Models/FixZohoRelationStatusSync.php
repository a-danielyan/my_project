<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FixZohoRelationStatusSync extends Model
{
    use HasFactory;

    protected $table = 'fix_zoho_relation_status_sync';
    protected $fillable = [
        'entity_type',
        'entity_id',
        'synced_at',
    ];

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }
}
