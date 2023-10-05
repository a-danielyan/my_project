<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int job_id
 * @property string status
 * @property string module
 * @property string error
 * @property string filename
 */
class ZohoBulkImportJobs extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';
    public const STATUS_FAILED = 'failed';
    public const STATUS_DOWNLOADED = 'downloaded';
    public const STATUS_INSERTED = 'inserted';

    protected $table = 'zoho_bulk_import_job';

    protected $fillable = [
        'job_id',
        'status',
        'error',
    ];
}
