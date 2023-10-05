<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int user_id
 * @property int|null impersonate_user_id
 * @property string status
 * @property string|Carbon activity_time
 * @property string|null user_ip_address
 */
class UserLastLoginActivity extends Model
{
    use HasFactory;

    protected $table = 'user_last_login_activity';

    protected $fillable = [
        'user_id',
        'impersonate_user_id',
        'status',
        'user_ip_address',
        'activity_time',
    ];


    protected array $dates = [
        'activity_time',
    ];


    public $timestamps = false;
}
