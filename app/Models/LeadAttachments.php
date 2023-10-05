<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int lead_id
 * @property string name
 * @property string attachment_file
 * @property string attachment_link
 */
class LeadAttachments extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lead_attachment';

    protected $fillable = [
        'lead_id',
        'name',
        'attachment_file',
        'attachment_link',
        'created_by',
        'updated_by',
    ];
}
