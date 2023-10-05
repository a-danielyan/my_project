<?php

namespace App\Models;

use App\Models\Interfaces\FilteredInterface;
use App\Traits\CreatedByUpdatedByTrait;
use App\Traits\FilterScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\Permission\PermissionRegistrar;

/**
 * @property int id
 * @property string name
 * @property string description
 * @property int created_by
 * @property int updated_by
 * @property Collection permissions
 * @property User|null createdBy
 * @property User|null updatedBy
 *
 */
class Role extends Model implements FilteredInterface
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;
    use FilterScopeTrait;

    public const MAIN_ADMINISTRATOR_ROLE = 'Administrator';
    public const STANDARD_USER_ROLE = 'Standard User';
    public const STATUS_DISABLED = 'Disabled';

    protected $table = 'role';

    protected $fillable = [
        'name',
        'description',
        'created_by',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'updated_by');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            RoleHasPermission::class,
            PermissionRegistrar::$pivotRole,
            PermissionRegistrar::$pivotPermission,
        )->with('customField');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

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
