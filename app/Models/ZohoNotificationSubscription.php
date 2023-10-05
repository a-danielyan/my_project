<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int chanel_id
 * @property string module
 * @property Carbon expired_at
 */
class ZohoNotificationSubscription extends Model
{
    use HasFactory;

    protected $table = 'zoho_notification_subscription';
    protected $fillable = [
        'chanel_id',
        'module',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'date',
    ];
}
