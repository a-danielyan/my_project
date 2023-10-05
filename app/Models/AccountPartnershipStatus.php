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
 */
class AccountPartnershipStatus extends Model implements FilteredInterface
{
    use HasFactory;
    use SoftDeletes;
    use FilterScopeTrait;

    protected $table = 'account_partnership_status';
    public const STATUS_DISABLED = 'Disabled';

    protected $fillable = [
        'name',
        'status',
        'created_by',
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
