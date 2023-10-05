<?php

namespace App\Http\Filter;

use App\Helpers\DBHelper;
use App\Models\Interfaces\FilteredInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

abstract class AbstractFilter
{
    abstract public function filterArray(): array;

    public function filter(Builder $query, array $params): Builder
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
     * @param mixed $value
     * @param array $params
     * @return Builder
     */
    protected function modifyQueryWithFieldAndValue(Builder $query, string $field, mixed $value, array $params): Builder
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
                $this->attachCustomFilter($query, $field, $value, $params);

                break;
            case $this->canUseForArraySearch($field):
                $this->attachArraySearch($query, $field, $value);

                break;
            default:
                break;
        }

        return $query;
    }

    protected function canUseForEqual(string $key): bool
    {
        return $this->canUseKeyForOperation(FilteredInterface::OPERATION_EQUAL, $key);
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
        $filterArray = $this->filterArray()[FilteredInterface::OPERATION_RANGE][$field];

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
            array_key_exists(FilteredInterface::OPERATION_SELECT, $this->filterArray()) &&
            array_key_exists($field, $this->filterArray()[FilteredInterface::OPERATION_SELECT])
        ) {
            if (is_string($value)) {
                foreach (explode(',', $value) as $item) {
                    $result = in_array($item, $this->filterArray()[FilteredInterface::OPERATION_SELECT][$field], true);
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

    private function canUseKeyForOperationJSON(string $key, bool $isBoolean = true): bool|string
    {
        if (array_key_exists(FilteredInterface::OPERATION_JSON, $this->filterArray())) {
            foreach ($this->filterArray()[FilteredInterface::OPERATION_JSON] as $row => $value) {
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
        return array_key_exists(FilteredInterface::OPERATION_RANGE, $this->filterArray()) &&
            array_key_exists($field, $this->filterArray()[FilteredInterface::OPERATION_RANGE]);
    }

    private function canUseForCustom(string $field): bool
    {
        return array_key_exists(FilteredInterface::OPERATION_CUSTOM, $this->filterArray()) &&
            in_array($field, $this->filterArray()[FilteredInterface::OPERATION_CUSTOM], true);
    }

    public function sortableFields(): array
    {
        return $this->filterArray()['sort'] ?? [];
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
        return $this->canUseKeyForOperation(FilteredInterface::OPERATION_LIKE, $key);
    }

    protected function canUseForArraySearch($key): bool
    {
        return $this->canUseKeyForOperation(FilteredInterface::OPERATION_ARRAY_SEARCH, $key);
    }

    protected function canUseForRelation(string $key): bool
    {
        return array_key_exists(FilteredInterface::OPERATION_RELATION, $this->filterArray())
            && array_key_exists($key, $this->filterArray()[FilteredInterface::OPERATION_RELATION]);
    }

    protected function canUseForJSON(string $key, bool $isBoolean = true): bool|string
    {
        return $this->canUseKeyForOperationJSON($key, $isBoolean);
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
        $r_array = $this->filterArray()['relation'][$field];

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
            if (!is_array($value)) {
                $value = explode(',', $value);
            }

            return $query->whereHas(
                $r_array['eloquent_m'],
                static function (Builder $q) use ($r_array, $value) {
                    return $q->whereIn($q->getModel()->getTable() . '.' . $r_array['where'], $value);
                },
            );
        }

        return $query;
    }

    protected function canUseForBoolean(string $key): bool
    {
        return $this->canUseKeyForOperation(FilteredInterface::OPERATION_BOOLEAN, $key);
    }

    /**
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    private function attachSort(Builder $query, array $params): Builder
    {
        $order = (array_key_exists('order', $params) && $params['order'] === 'asc') ? 'asc' : 'desc';
        $sort = FilteredInterface::DEFAULT_SORT_FIELD;

        if (
            array_key_exists(FilteredInterface::OPERATION_SORT, $params)
            && $params[FilteredInterface::OPERATION_SORT]
        ) {
            $field = $params[FilteredInterface::OPERATION_SORT];

            switch ($field) {
                case $this->canSort($field):
                    $fieldInDB = array_search($field, $this->filterArray()[FilteredInterface::OPERATION_SORT]);

                    if (is_string($fieldInDB)) {
                        $sort = $fieldInDB;
                    } else {
                        $sort = $this->snakeField($field);
                    }

                    $query->orderBy($sort, $order);

                    break;
                case $this->canSortForCustomSort($field):
                    $sort = $this->snakeField($field);

                    $this->attachCustomSort($query, $sort, $order);

                    break;
            }
        } else {
            $query->orderBy($query->getModel()->getTable() . '.' . $sort, $order);
        }

        return $query;
    }

    protected function canSort(string $field): bool
    {
        return array_key_exists(FilteredInterface::OPERATION_SORT, $this->filterArray()) &&
            in_array($field, $this->filterArray()[FilteredInterface::OPERATION_SORT], true);
    }

    protected function canSortForCustomSort(string $field): bool
    {
        return array_key_exists(FilteredInterface::SORT_CUSTOM, $this->filterArray()) &&
            in_array($field, $this->filterArray()[FilteredInterface::SORT_CUSTOM], true);
    }

    /**
     * @param Builder $query
     * @param string $field
     * @param string $order
     * @return Builder
     */
    protected function attachCustomSort(Builder $query, string $field, string $order): Builder
    {
        return $query;
    }

    /**
     * @param Builder $query
     * @param string $field
     * @param string $value
     * @param array $params
     * @return Builder
     */
    public function attachCustomFilter(Builder $query, string $field, string $value, array $params): Builder
    {
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
        $filterArrayKey = array_search($field, $this->filterArray()[FilteredInterface::OPERATION_EQUAL]);

        $query->where(function (Builder $query) use ($filterArrayKey, $value, $field) {
            if (is_string($filterArrayKey)) {
                $this->whereFieldArrayKey($query, $filterArrayKey, $value);
            } else {
                $query->where($query->getModel()->getTable() . '.' . $this->snakeField($field), $value);
            }
        });

        return $query;
    }

    protected function attachArraySearch(Builder $query, string $field, mixed $value): void
    {
        if (!is_array($value)) {
            $value = explode(',', $value);
        }

        $query->where(function (Builder $query) use ($value, $field) {
            $column = $query->getModel()->getTable() . '.' . $this->snakeField($field);
            if (count($value) === 1) {
                $query->where($column, head($value));
            } else {
                $query->whereIn($column, $value);
            }
        });
    }
}
