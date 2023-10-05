<?php

namespace App\Models;

use App\Traits\CreatedByUpdatedByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property string subject_text
 * @property User createdBy
 * @property User updatedBy
 */
class SubjectLine extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;

    protected $table = 'subject_line';
    protected $fillable = [
        'subject_text',
        'created_by',
        'updated_by',
    ];
}
