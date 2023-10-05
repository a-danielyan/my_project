<?php

namespace App\Models\Interfaces;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User;

/**
 * Interface ScopePermissionInterface
 * @package App\Models\Interfaces
 *
 * @method Builder permission(User $user, bool $with = true, bool $onlyWritePermission = false)
 * @property Collection groupsData
 */
interface WithGroupPermissionInterface
{
    public static function getGroupAssociationModel(): string;

    /**
     * Permission filter
     *
     * @param Builder $query
     * @param User $user
     * @param bool $with
     * @param bool $checkWritePermission
     * @return Builder
     */
    public static function scopePermission(
        Builder $query,
        User $user,
        bool $with = true,
        bool $checkWritePermission = false
    ): Builder;
}
