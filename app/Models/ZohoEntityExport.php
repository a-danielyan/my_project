<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZohoEntityExport extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'NEW';
    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    public const STATUS_DONE = 'DONE';
    public const STATUS_ERROR = 'ERROR';

    protected $table = 'zoho_entity_exports';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'data',
        'sync_status',
        'error',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
