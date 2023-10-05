<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int id
 * @property int $user_id
 * @property string $access_token
 * @property string $grant_token
 * @property string $refresh_token
 * @property Carbon $expire_on
 * @property string $service
 * @property string user_name
 * @property string client_id
 * @property string client_secret
 * @property string expires_in
 * @property string redirect_url
 * @property bool is_expired
 */
class OauthToken extends Model
{
    use HasFactory;

    protected $table = 'oauth_token';
    public const SERVICE_NAME_GOOGLE_MAIL = 'Gmail';

    protected $fillable = [
        'user_id',
        'access_token',
        'grant_token',
        'refresh_token',
        'expire_on',
        'service',
        'user_name',
        'client_id',
        'client_secret',
        'expires_in',
        'redirect_url'
    ];

    protected $casts = [
        'expire_on' => 'date',
    ];

    public function getIsExpiredAttribute(): bool
    {
        return $this->expire_on && ($this->expire_on <= Carbon::now()->addMinutes(5));
    }
}
