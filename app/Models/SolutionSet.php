<?php

namespace App\Models;

use App\Models\Interfaces\FilteredInterface;
use App\Traits\CreatedByUpdatedByTrait;
use App\Traits\FilterScopeTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string name
 * @property int id
 * @property Collection items
 */
class SolutionSet extends Model implements FilteredInterface
{
    use HasFactory;
    use CreatedByUpdatedByTrait;
    use SoftDeletes;
    use FilterScopeTrait;

    protected $table = 'solution_set';

    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SolutionSetItems::class);
    }

    public static function filterArray(): array
    {
        return [
            'equal' => [
                'id',
                'name',
            ],
            'sort' => [
                'id',
                'name',
            ],
        ];
    }
}
