<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property PublishDetail publicLink
 * @property string ip
 * @property string user_agent
 * @property int id
 * @property Carbon created_at
 */
class PublicLinkAccessLog extends Model
{
    use HasFactory;

    protected $table = 'public_link_access_log';
    protected $fillable = [
        'publish_detail_id',
        'ip',
        'user_agent',
    ];

    public function publicLink(): BelongsTo
    {
        return $this->belongsTo(PublishDetail::class, 'publish_detail_id');
    }
}
