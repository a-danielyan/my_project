<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int invoice_id
 */
class InvoiceAttachment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'invoice_attachment';

    protected $fillable = [
        'invoice_id',
        'name',
        'attachment_file',
        'attachment_link',
        'created_by',
        'updated_by',
    ];
}
