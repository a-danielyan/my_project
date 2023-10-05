<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int channel_id
 * @property string module
 * @property array notification_data
 * @property string processing_status
 * @property string error_message
 */
class ZohoNotificationEvents extends Model
{
    use HasFactory;

    protected $table = 'zoho_notification_event';
    protected $fillable = [
        'channel_id',
        'module',
        'notification_data',
        'processing_status',
        'error_message',
    ];

    protected $casts = [
        'notification_data' => 'array',
    ];
}
