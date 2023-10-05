<?php

namespace App\Models;

use App\Traits\CreatedByUpdatedByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int estimate_id
 */
class EstimateAttachment extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;

    protected $table = 'estimate_attachment';

    protected $fillable = [
        'estimate_id',
        'name',
        'attachment_file',
        'attachment_link',
        'created_by',
        'updated_by',
    ];
}
