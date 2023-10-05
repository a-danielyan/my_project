<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string name
 * @property string entity
 * @property array settings
 *
 */
class Preference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'entity',
        'name',
        'settings',
    ];


    protected $casts = [
        'settings' => 'array',
    ];
}
