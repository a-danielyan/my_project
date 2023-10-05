<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int user_id
 * @property int|null impersonate_user_id
 * @property string|Carbon activity_time
 * @property string|null user_ip_address
 */
class UserLastLogin extends Model
{
    use HasFactory;

    protected $table = 'user_last_login';
    protected $fillable = [
        'user_id',
        'impersonate_user_id',
        'user_ip_address',
    ];

    protected array $dates = [
        'activity_time',
    ];

    /**
     * Prevent timestamps
     *
     * @var bool
     */
    public $timestamps = false;
}
