<?php

namespace App\Models;

use App\Traits\CreatedByUpdatedByTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property int account_id
 * @property Carbon demo_date
 * @property User trainedBy
 * @property User createdBy
 * @property User updatedBy
 * @property string note
 * @property Activity activity
 */
class AccountDemo extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;

    protected $table = 'account_demo';

    protected $fillable = [
        'account_id',
        'demo_date',
        'trained_by',
        'created_by',
        'updated_by',
        'note',
        'activity_id',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function trainedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trained_by');
    }
}
