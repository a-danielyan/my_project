<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int contact_id
 */
class ContactAttachments extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'contact_attachment';

    protected $fillable = [
        'contact_id',
        'name',
        'attachment_file',
        'attachment_link',
        'created_by',
        'updated_by',
    ];
}
