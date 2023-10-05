<?php

namespace App\Models;

use App\Models\Interfaces\FilteredInterface;
use App\Traits\CreatedByUpdatedByTrait;
use App\Traits\FilterScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property string name
 * @property string status
 * @property int created_by
 */
class ContactAuthority extends Model implements FilteredInterface
{
    use HasFactory;
    use SoftDeletes;
    use FilterScopeTrait;
    use CreatedByUpdatedByTrait;

    protected $table = 'contact_authority';
    public const STATUS_DISABLED = 'Disabled';

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
            ],
            'like' => [
            ],
            'sort' => [
                'id',
            ],
            'relation' => [
            ],
            'custom' => [
                'status',
            ],
            'custom_sort' => [],
        ];
    }
}
