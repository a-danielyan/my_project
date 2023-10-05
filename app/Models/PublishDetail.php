<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property-read int id
 * @property string token
 * @property string entity_type
 * @property int entity_id
 * @property Carbon expire_on
 * @property string created_by
 * @property string updated_by
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * @property User user
 */
class PublishDetail extends AuthUser implements JWTSubject
{
    public const ENTITY_TYPE_PROPOSAL = 'Proposal';
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'publish_details';

    protected $fillable = [
        'token',
        'entity_type',
        'entity_id',
        'expire_on',
        'created_by',
        'updated_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function getAuthIdentifierName(): string
    {
        return 'created_by';
    }

    public function getJWTIdentifier(): int
    {
        return $this->created_by;
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PublicLinkAccessLog::class);
    }
}
