<?php

namespace App\Models\Interfaces;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface FilteredInterface
 * @package App\Models\Interfaces
 *
 * @method static Builder filter(array $params)
 */
interface FilteredInterface
{
    public const OPERATION_EQUAL = 'equal';

    public const OPERATION_LIKE = 'like';

    public const OPERATION_RELATION = 'relation';

    public const OPERATION_BOOLEAN = 'boolean';

    public const OPERATION_SELECT = 'select';

    public const OPERATION_JSON = 'json';

    public const OPERATION_RANGE = 'range';

    public const OPERATION_CUSTOM = 'custom';

    public const SORT_CUSTOM = 'custom_sort';

    public const OPERATION_SORT = 'sort';

    public const DEFAULT_SORT_FIELD = 'updated_at';
    public const CUSTOM_SORT_FIELD = 'id';

    public const OPERATION_ARRAY_SEARCH = 'array_search';

    public const RANGE_OPERATION_FROM = '>';
    public const RANGE_OPERATION_TO = '<';

    public function scopeFilter(Builder $query, array $params): Builder;
}
