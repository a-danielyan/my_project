<?php

namespace App\Models;

use App\Traits\CreatedByUpdatedByTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property string name
 * @property int account_id
 * @property Carbon created_at
 * @property string attachment_file
 * @property string attachment_link
 * @property User|null createdBy
 * @property User|null updatedBy
 */
class AccountAttachment extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;

    protected $table = 'account_attachment';

    protected $fillable = [
        'account_id',
        'name',
        'attachment_file',
        'attachment_link',
        'created_by',
        'updated_by',
    ];
}
