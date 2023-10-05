<?php

namespace App\Models;

use App\Models\Interfaces\FilteredInterface;
use App\Traits\FilterScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property string name
 * @property string status
 * @property User created_by
 * @property User|null updated_by
 *
 */
class Solutions extends Model implements FilteredInterface
{
    use HasFactory;
    use SoftDeletes;
    use FilterScopeTrait;

    public const STATUS_DISABLED = 'Disabled';
    protected $table = 'solution';
    protected $fillable = [
        'name',
        'status',
        'created_by',
        'updated_by',
    ];

    public static function filterArray(): array
    {
        return [
            'equal' => [
                'id',
                'name',
            ],
            'like' => [
            ],
            'sort' => [
                'id',
                'name'
            ],
            'relation' => [
                'tag' => [
                    'eloquent_m' => 'tagsAssociation.masterTagsData',
                    'where' => 'tag',
                ],
            ],
            'custom' => [
                'status',
            ],
            'custom_sort' => [],
        ];
    }
}
