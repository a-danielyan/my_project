<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property array raw_data
 * @property string processing_status
 * @property string error_message
 */
class MailgunNotificationRawData extends Model
{
    use HasFactory;

    protected $table = 'mailgun_notification_raw_data';

    protected $fillable = ['raw_data', 'processing_status', 'error_message'];

    protected $casts = [
        'raw_data' => 'array',
    ];
}
