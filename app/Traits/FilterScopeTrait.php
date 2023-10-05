<?php

namespace App\Traits;

use App\Helpers\DBHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;

/**
 * Trait FilterScopeTrait
 * @package App\Traits
 *
 * @method static Builder filter(User $user, bool $onlyWritePermission = false)
 * @method sortByCategory(Builder $query, string $order)
 */
trait FilterScopeTrait
{
    /**
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $params): Builder
    {
        foreach ($params as $key => $value) {
            $this->modifyQueryWithFieldAndValue($query, $key, $value, $params);
        }

        $this->attachSort($query, $params);

        return $query;
    }

    /**
     * @param Builder $query
     * @param string $field
     * @param $value
     * @param array $params
     * @return Builder
     */
    protected function modifyQueryWithFieldAndValue(Builder $query, string $field, $value, array $params): Builder
    {
        switch ($field) {
            case $this->canUseForEqual($field):
                $this->attachEquals($query, $field, $value);

                break;
            case $this->canUseForLike($field):
                $exact = array_key_exists('exact', $params);
                $values = explode(',', $value);

                if (count($values) > 1) {
                    $this->attachLike($query, $field, $values, $exact);
                } else {
                    if ($exact) {
                        $query->where($this->fieldWithTableName($query, $field), '=', $value);
                    } else {
                        $query->where(
                            $this->fieldWithTableName($query, $field),
                            'LIKE',
                            '%' . DBHelper::escapeForLike($value) . '%',
                        );
                    }
                }

                break;
            case $this->canUseForRelation($field):
                $this->attachRelation($query, $field, $value);

                break;
            case $this->canUseForBoolean($field):
                if ($value === 1) {
                    $query->where('expiry_date', '<', date('Y-m-d'));
                }

                break;
            case $this->canUseForSelect($field, $value):
                $query->select(...explode(',', $value));

                break;
            case $this->canUseForJSON($field):
                $name = $this->canUseForJSON($field, false);
                $query->where($name, $value);

                break;
            case $this->canUseForRange($field):
                $this->attachWhereWithIf($query, $field, $value);

                break;
            case $this->canUseForCustom($field):
                $this->attachCustomFilter($query, $field, $value);

                break;
            case $this->canUseForArraySearch($field):
                if (!is_array($value)) {
                    $value = explode(',', $value);
                }

                $query->whereIn($this->snakeField($field), $value);

                break;
            default:
                break;
        }

        return $query;
    }

    protected function canUseForEqual(string $key): bool
    {
        return $this->canUseKeyForOperation(self::OPERATION_EQUAL, $key);
    }

    protected function attachLike(Builder $query, string $field, array $values, bool $exact): Builder
    {
        foreach ($values as $val) {
            if ($exact) {
                $query->orWhere($this->fieldWithTableName($query, $field), '=', $val);
            } else {
                $query->orWhere(
                    $this->fieldWithTableName($query, $field),
                    'LIKE',
                    '%' . DBHelper::escapeForLike($val) . '%',
                );
            }
        }

        return $query;
    }

    protected function attachWhereWithIf(Builder $query, string $field, string $value): Builder
    {
        $filterArray = $this->filterArray()[self::OPERATION_RANGE][$field];

        $dbColumn = $filterArray['field'];
        $dbOperation = $filterArray['operation'];

        $query->where($dbColumn, $dbOperation, $value);

        return $query;
    }

    /**
     * @param Builder $query
     * @param string $field
     * @param string $value
     * @return Builder
     */
    protected function whereFieldArrayKey(Builder $query, string $field, string $value): Builder
    {
        if ($value === 'valid') {
            $query->where($field, 1);
        } elseif ($value === 'invalid') {
            $query->where($field, 0);
        } else {
            $query->where($this->snakeField($field), $value);
        }

        return $query;
    }

    protected function canUseForSelect(string $field, mixed $value): bool
    {
        if (
            array_key_exists(self::OPERATION_SELECT, $this->filterArray()) &&
            array_key_exists($field, $this->filterArray()[self::OPERATION_SELECT])
        ) {
            if (is_string($value)) {
                foreach (explode(',', $value) as $item) {
                    $result = in_array($item, $this->filterArray()[self::OPERATION_SELECT][$field], true);
                }
            }

            return $result ?? false;
        }

        return false;
    }

    private function canUseKeyForOperation(string $operation, string $key): bool
    {
        return array_key_exists($operation, $this->filterArray()) &&
            in_array($key, $this->filterArray()[$operation], true);
    }

    private function canUseKeyForOperationJSON(string $operation, string $key, bool $isBoolean = true): bool|string
    {
        if (array_key_exists($operation, $this->filterArray())) {
            foreach ($this->filterArray()[$operation] as $row => $value) {
                if (in_array($key, $value, true)) {
                    if ($isBoolean) {
                        return true;
                    }

                    return $row . '->' . $this->snakeField($key);
                }
            }
        }

        return false;
    }

    private function canUseKeyForOperationRange(string $field): bool
    {
        return array_key_exists(self::OPERATION_RANGE, $this->filterArray()) &&
            array_key_exists($field, $this->filterArray()[self::OPERATION_RANGE]);
    }

    private function canUseForCustom(string $field): bool
    {
        return array_key_exists(self::OPERATION_CUSTOM, $this->filterArray()) &&
            in_array($field, $this->filterArray()[self::OPERATION_CUSTOM], true);
    }

    /**
     * Array with params for filtering
     *
     * @return array
     */
    abstract public static function filterArray(): array;

    public static function sortableFields(): array
    {
        return self::filterArray()['sort'] ?? [];
    }

    protected function snakeField(?string $field): string
    {
        return Str::snake($field);
    }

    protected function fieldWithTableName(Builder $query, ?string $field): string
    {
        $modelTable = $query->getModel()->getTable();

        return $modelTable . '.' . $this->snakeField($field);
    }

    protected function canUseForLike($key): bool
    {
        return $this->canUseKeyForOperation(self::OPERATION_LIKE, $key);
    }

    protected function canUseForArraySearch($key): bool
    {
        return $this->canUseKeyForOperation(self::OPERATION_ARRAY_SEARCH, $key);
    }

    protected function canUseForRelation(string $key): bool
    {
        return array_key_exists(self::OPERATION_RELATION, $this->filterArray())
            && array_key_exists($key, $this->filterArray()[self::OPERATION_RELATION]);
    }

    protected function canUseForJSON(string $key, bool $isBoolean = true): bool|string
    {
        return $this->canUseKeyForOperationJSON(self::OPERATION_JSON, $key, $isBoolean);
    }

    protected function canUseForRange(string $field): bool
    {
        return $this->canUseKeyForOperationRange($field);
    }


    /**
     * @param Builder $query
     * @param string $field
     * @param $value
     * @return Builder
     */
    protected function attachRelation(Builder $query, string $field, $value): Builder
    {
        $r_array = $this::filterArray()['relation'][$field];

        // TODO: refactor to type and target field

        if (isset($r_array['like'])) {
            return $query->whereHas(
                $r_array['eloquent_m'],
                static function (Builder $q) use ($r_array, $value) {
                    return $q->where($r_array['like'], 'LIKE', '%' . DBHelper::escapeForLike($value) . '%');
                },
            );
        }

        if (isset($r_array['where'])) {
            $value = explode(',', $value);

            return $query->whereHas(
                $r_array['eloquent_m'],
                static function (Builder $q) use ($r_array, $value) {
                    if (isset($r_array['exclude']) && $r_array['exclude']) {
                        return $q->whereNotIn($q->getModel()->getTable() . '.' . $r_array['where'], $value);
                    }

                    return $q->whereIn($q->getModel()->getTable() . '.' . $r_array['where'], $value);
                },
            );
        }

        return $query;
    }

    protected function canUseForBoolean(string $key): bool
    {
        return $this->canUseKeyForOperation(self::OPERATION_BOOLEAN, $key);
    }

    /**
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    private function attachSort(Builder $query, array $params): Builder
    {
        $order = (array_key_exists('order', $params) && $params['order'] === 'asc') ? 'asc' : 'desc';
        $sort = defined('static::CUSTOM_SORT_FIELD') ? static::CUSTOM_SORT_FIELD : self::DEFAULT_SORT_FIELD;

        if (array_key_exists(self::OPERATION_SORT, $params) && $params[self::OPERATION_SORT]) {
            $field = $params[self::OPERATION_SORT];

            switch ($field) {
                case $this->canSort($field):
                    $fieldInDB = array_search($field, $this::filterArray()[self::OPERATION_SORT]);

                    if (is_string($fieldInDB)) {
                        $sort = $fieldInDB;
                    } else {
                        $sort = $this->snakeField($field);
                    }

                    $query->orderBy($sort, $order);

                    break;
                case $this->canSortForRelation($field):
                    $sort = $this->snakeField($field);

                    $this->attachCustomSort($query, $sort, $order);

                    break;
            }
        } else {
            $query->orderBy($sort, $order);
        }

        return $query;
    }

    protected function canSort(string $field): bool
    {
        return array_key_exists(self::OPERATION_SORT, $this::filterArray()) &&
            in_array($field, $this::filterArray()[self::OPERATION_SORT], true);
    }

    protected function canSortForRelation(string $field): bool
    {
        return array_key_exists(self::SORT_CUSTOM, $this::filterArray()) &&
            in_array($field, $this::filterArray()[self::SORT_CUSTOM], true);
    }

    /**
     * @param Builder $query
     * @param string $field
     * @param string $order
     * @return Builder
     */
    protected function attachCustomSort(Builder $query, string $field, string $order): Builder
    {
        switch ($field) {
            case 'group':
                $this->sortByGroup($query, $order);

                break;
            case 'email':
                $this->sortByEmail($query, $order);

                break;
            case 'full_name':
                $this->sortByFullName($query, $order);

                break;
            case 'last_login':
                $this->sortByLastLogin($query, $order);

                break;

            case 'last_auth_activity':
                $this->sortByLastAuthActivity($query, $order);

                break;

            case 'role':
                $this->sortByRole($query, $order);

                break;

            case 'username':
                $this->sortByUsername($query, $order);

                break;
            default:
                break;
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param string $field
     * @param array|string $value
     * @return Builder
     */
    public function attachCustomFilter(Builder $query, string $field, array|string $value): Builder
    {
        switch ($field) {
            case 'fullName':
                $this->filterFullName($query, $value);

                break;

            case 'username':
                $this->filterByUsername($query, $value);

                break;
            case 'search':
                $this->filterBySearch($query, $value);

                break;
            case 'status':
                $this->filterByStatus($query, $value);

                break;
            case 'activityStatus':
                $this->filterByActivityStatus($query, $value);

                break;
            case 'relatedToEntity':
                $this->filterByRelatedToEntity($query, $value);

                break;
            case 'relatedToId':
                $this->filterByRelatedToId($query, $value);

                break;
            case 'afterDate':
                $this->filterByAfterDate($query, $value);

                break;
            case 'beforeDate':
                $this->filterByBeforeDate($query, $value);

                break;
            case 'entityType':
                $this->filterByEntityType($query, $value);

                break;
            case 'entity':
                $this->filterByEntity($query, $value);
                break;

            case 'product':
                $this->filterByProduct($query, $value);
                break;

            case 'tag':
                $this->filterByTag($query, $value);
                break;

            case 'group':
                $this->filterByGroup($query, $value);
                break;

            default:
                break;
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param string $field
     * @param mixed $value
     * @return Builder
     */
    private function attachEquals(Builder $query, string $field, mixed $value): Builder
    {
        $filterArrayKey = array_search($field, $this->filterArray()[self::OPERATION_EQUAL]);

        $query->where(function ($query) use ($filterArrayKey, $value, $field) {
            if (is_string($filterArrayKey)) {
                $this->whereFieldArrayKey($query, $filterArrayKey, $value);
            } else {
                if (in_array($field, $this->fillable)) {
                    if (is_array($value)) {
                        $query->whereIn($this->table . '.' . $this->snakeField($field), $value);
                    } else {
                        $query->where($this->table . '.' . $this->snakeField($field), $value);
                    }
                } else {
                    $query->where($this->snakeField($field), $value);
                }
            }
        });

        return $query;
    }

    protected function filterByStatus(Builder $query, array|string $value): Builder
    {
        $query->where(function ($query) use ($value) {
            foreach ($value as $status) {
                if ($status === self::STATUS_DISABLED) {
                    $query->orWhereNotNull('deleted_at');
                } else {
                    $query->orWhere('status', $status);
                }
            }
        });

        return $query;
    }

    protected function filterByEntityType(Builder $query, string|array $value): Builder
    {
        if (is_array($value)) {
            $value = array_map(function ($element) {
                return 'App\Models\\' . $element;
            }, $value);

            $query->whereIn('entity_type', $value);
        } else {
            $query->where('entity_type', 'App\Models\\' . $value);
        }

        return $query;
    }

    protected function filterByTag(Builder $query, string|array $value)
    {
        if (is_array($value)) {
            $query->whereHas('tag', function ($query) use ($value) {
                $query->whereIn('tag', $value);
            });
        } else {
            $query->whereHas('tag', function ($query) use ($value) {
                $query->where('tag', $value);
            });
        }

        return $query;
    }
}
