<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string status
 * @property Carbon created_at
 */
class InvoiceStatusLog extends Model
{
    use HasFactory;

    protected $table = 'invoice_status_log';
    protected $fillable = [
        'invoice_id',
        'status',
    ];
}
