<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string name
 * @property int size
 * @property string key
 * @property string type
 * @property string target
 * @property string disk
 * @property bool broken
 * @property string media_type
 * @property int media_id
 */
class TusFileData extends Model
{
    use HasFactory;

    public const TARGET_SOURCE = 'source';
    public const TARGET_DATA = 'data';
    public const TARGET_USER_DATA = 'user_data';

    protected array $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'size',
        'type',
        'key',
        'target',
        'disk',
    ];

    protected $table = 'tus_file_data';
}
