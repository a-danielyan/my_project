<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int id
 * @property int custom_field_id
 * @property RoleHasPermission roleHasPermission
 * @property CustomField customField
 * @property string action
 * @property Collection roles
 */
class Permission extends Model
{
    use HasFactory;

    public const ACTION_CREATE = 'create';
    public const ACTION_READ = 'read';
    public const ACTION_UPDATE = 'update';
    public const ACTION_BULK_UPDATE = 'bulkUpdate';
    public const ACTION_DELETE = 'delete';

    protected $table = 'permission';

    protected $fillable = [
        'custom_field_id',
        'action',
    ];

    public function roleHasPermission(): HasMany
    {
        return $this->hasMany(RoleHasPermission::class);
    }

    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class)->withTrashed();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            RoleHasPermission::class,
        );
    }

    public static function findByType($entityType, $permissionAction, ?string $fieldName)
    {
        $query = self::query()->whereHas('customField', function ($query) use ($entityType, $fieldName) {
            $query->where('entity_type', $entityType);
            if ($fieldName) {
                $query->where('name', $fieldName); //@todo replace with code?
            }
        })->with(['roles']);

        $query = $query->where('action', $permissionAction);

        return $query->first();
    }

    public static function findPermissionByRole($entityType, $permissionAction, Role $role, ?string $fieldName)
    {
        $query = self::query()->whereHas('customField', function ($query) use ($entityType, $fieldName) {
            $query->where('entity_type', $entityType);
            if ($fieldName) {
                $query->where('name', $fieldName); //@todo replace with code?
            }
        })->whereHas('roles', function ($query) use ($role) {
            $query->where('id', $role->id);
        });

        $query = $query->where('action', $permissionAction);


        return $query->get();
    }
}
