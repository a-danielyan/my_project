<?php

namespace App\Http\Repositories;

use App\Exceptions\CustomErrorException;
use App\Http\Repositories\CustomField\CustomFieldValueRepository;
use App\Models\CustomField;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BaseRepositoryWithCustomFields extends BaseRepository
{
    protected CustomFieldValueRepository $customFieldValueRepository;
    protected CustomFieldRepository $customFieldRepository;

    public function __construct(
        Model $model,
        CustomFieldValueRepository $customFieldValueRepository,
        CustomFieldRepository $customFieldRepository,
    ) {
        $this->model = $model;
        $this->customFieldValueRepository = $customFieldValueRepository;
        $this->customFieldRepository = $customFieldRepository;
    }

    /**
     * @param array $params
     * @param User $user
     * @param array $relation
     * @return LengthAwarePaginator
     * @throws CustomErrorException
     * @throws ValidationException
     */
    public function getAllWithCustomFields(array $params, User $user, array $relation = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->withTrashed();
        $tableName = app($this->model::class)->getTable();
        $entityName = $this->model::class;
        $query->select($tableName . '.id');
        $availableCustomFields = CustomField::query()->where('entity_type', $entityName)
            ->where('type', '!=', CustomField::FIELD_TYPE_CONTAINER)->get(['id', 'code', 'name', 'type']);

        $availableFieldsGroupByCodes = $availableCustomFields->keyBy('code')->toArray();
        $modelFillableFields = app($this->model::class)->getFillable();

        $query = $this->applySort($params, $availableCustomFields, $query, $entityName);
        $query = $this->applyStatusFilter($params, $query, $tableName);
        $query = $this->applyTagFilter($params, $query);
        unset($params['status']);
        $query = $this->applySearchFilter($query, $params, $modelFillableFields);
        $query = $this->applyCustomFieldFilter($query, $params, $availableFieldsGroupByCodes);


        $query = $this->applyAgGridFilter($query, $params, $availableFieldsGroupByCodes);


        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;

        try {
            $totalRecords = $query->count();
            $recordsUniq = $query->skip(($page - 1) * $limit)->take($limit)->get();
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            throw ValidationException::withMessages(['sort' => 'Unknown sort column ', 'error' => $e->getMessage()]);
        }

        $uniqueSortedId = $recordsUniq->pluck('id');

        $allowedFieldsForUser = $this->getAllowedCustomFields($entityName, $user->role)->pluck('id')->toArray();

        $uniqueSortedIdArr = $uniqueSortedId->toArray();

        $filteredRecords = $this->model->newQuery()->with(
            array_merge(
                $relation,
                [
                    'customFields' => function ($query) use ($allowedFieldsForUser) {
                        $query->select([
                            'field_id',
                            'entity_id',
                            'text_value',
                            'boolean_value',
                            'integer_value',
                            'float_value',
                            'datetime_value',
                            'date_value',
                            'json_value',
                        ]);
                        $query->whereIn(
                            'field_id',
                            $allowedFieldsForUser,
                        );
                    },
                    'customFields.customField' => function ($query) {
                        $query->select([
                            'id',
                            'type',
                            'code',
                            'lookup_type',
                        ]);
                    },
                    'tag',
                    'customFields.relatedUser',
                    'createdBy',
                    'updatedBy',
                ],
            ),
        )
            ->select(array_merge(['id', 'created_at', 'status', 'deleted_at'], $modelFillableFields))
            ->whereIn('id', $uniqueSortedId)
            ->withTrashed();

        // we have data sorted by  our condition
        $resultsFiltered = $filteredRecords->get()->sortBy(
            function ($model) use ($uniqueSortedIdArr) {
                return array_search($model->getKey(), $uniqueSortedIdArr);
            },
        );


        return new \Illuminate\Pagination\LengthAwarePaginator(
            $resultsFiltered,
            $totalRecords,
            $limit,
            $page,
            ['path' => Paginator::resolveCurrentPath()]
        );
    }


    protected function getAllowedCustomFields(string $entityType, Role $role)
    {
        return $this->customFieldRepository->getAllowedFieldForRole($entityType, $role);
    }

    public function create(array $data): Model
    {
        $model = parent::create($data);
        // we must use have at leas one custom field to make proper model showing
        if (!isset($data['entity_type'])) {
            $data['entity_type'] = $model::class;
        }
        if (!isset($data['customFields'])) {
            $customField = CustomField::query()->where('entity_type', $model::class)
                ->whereNotIn('type', [CustomField::FIELD_TYPE_INTERNAL, CustomField::FIELD_TYPE_CONTAINER])->first();
            if ($customField) {
                $data['customFields'][$customField->code] = null;
            }
        }

        $this->customFieldValueRepository->saveCustomField($data, $model->getKey());

        return $model;
    }

    public function update(Model $model, array $data = [], bool $isTrashed = false): bool
    {
        parent::update($model, $data, $isTrashed);

        $this->customFieldValueRepository->saveCustomField($data, $model->getKey());

        return true;
    }

    private function applyStatusFilter(array $params, Builder $query, string $tableName): Builder
    {
        $mustShowSoftDeleted = false;
        foreach ($params as $parameter => $value) {
            if ($parameter === 'status' and in_array('status', $this->model->getFillable())) {
                //attach status filter;
                $query->where(function ($query) use ($value, $tableName, &$mustShowSoftDeleted) {
                    foreach ($value as $status) {
                        if ($status === User::STATUS_DISABLED) {
                            $query->orWhereNotNull($tableName . '.deleted_at');
                            $mustShowSoftDeleted = true;
                        } else {
                            $query->orWhere($tableName . '.status', $status);
                        }
                    }
                });
            }
        }

        if (!$mustShowSoftDeleted) {
            $query->whereNull($tableName . '.deleted_at');
        }

        return $query;
    }

    private function applyTagFilter(array $params, Builder $query): Builder
    {
        if (!empty($params['tag'])) {
            $query->whereHas('tag', function ($query) use ($params) {
                $query->where('tag', $params['tag']);
            });
        }

        return $query;
    }


    private function applyCustomFieldFilter(Builder $query, array $params, array $availableFieldsGroupByCodes): Builder
    {
        if (!empty(array_intersect(array_keys($params), array_keys($availableFieldsGroupByCodes)))) {
            $query->whereHas(
                'customFieldValues.customFieldValue',
                function ($query) use ($params, $availableFieldsGroupByCodes) {
                    foreach ($params as $parameter => $value) {
                        if (isset($availableFieldsGroupByCodes[$parameter])) {
                            $fieldType = $availableFieldsGroupByCodes[$parameter]['type'];
                            if ($fieldType === CustomField::FIELD_TYPE_INTERNAL) {
                                continue;
                            }
                            $field = CustomField::$attributeTypeFields[$fieldType];
                            if (is_array($value)) {
                                $query->whereIn($field, $value)->where(
                                    'field_id',
                                    $availableFieldsGroupByCodes[$parameter]['id'],
                                );
                            } else {
                                $query->where($field, $value)->where(
                                    'field_id',
                                    $availableFieldsGroupByCodes[$parameter]['id'],
                                );
                            }
                        }
                    }
                },
            );
        }
        foreach ($params as $parameter => $value) {
            if (in_array($parameter, $this->model->getFillable()) || $parameter === 'id') {
                if ($parameter === 'id') {
                    $tableName = app($this->model::class)->getTable();
                    $parameter = $tableName . '.id';
                }

                $query->where($parameter, $value);
            }
        }

        return $query;
    }

    private function applySort(
        array $params,
        Collection $availableCustomFields,
        Builder $query,
        string $entityName,
    ): Builder {
        $order = $params['order'] ?? 'desc';
        $sortBy = $params['sort'] ?? 'id';
        $tableName = app($this->model::class)->getTable();
        $sortByField = $availableCustomFields->filter(function ($item) use ($sortBy) {
            return ($item->code == $sortBy && $item->type !== CustomField::FIELD_TYPE_INTERNAL);
        })->first();
        if ($sortByField) {
            //we use sort by custom field
            $query->leftJoin(
                'custom_field_values AS sortJoin',
                function ($join) use ($tableName, $entityName, $sortByField) {
                    $join->on('sortJoin.entity_id', '=', $tableName . '.id');
                    $join->on('sortJoin.entity', '=', DB::raw("'" . addslashes($entityName) . "'"));
                    $join->on('sortJoin.field_id', '=', DB::raw($sortByField->id));
                },
            );
            $customFieldType = CustomField::$attributeTypeFields[$sortByField->type] ?? 'text_value';
            $query->orderBy('sortJoin.' . $customFieldType, $order);
        } else {
            $query->orderBy($tableName . '.' . $sortBy, $order);
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param array $params
     * @param array $modelFillableFields
     * @return Builder
     */
    public function applySearchFilter(Builder $query, array $params, array $modelFillableFields): Builder
    {
        if ($params['search'] ?? '') {
            $tableName = app($this->model::class)->getTable();
            $query->leftJoin('custom_field_values', $tableName . '.id', '=', 'custom_field_values.entity_id');
            $search = $params['search'];
            $query->where(function (Builder $query) use ($search, $modelFillableFields) {
                $query->where('custom_field_values.text_value', 'like', '%' . $search . '%');
                //         TODO do not clear if we need  search by int/float/date
                //        ->orWhere('SUB.integer_value', 'like', '%' . $search . '%')
                //        ->orWhere('SUB.float_value', 'like', '%' . $search . '%')
                //        ->orWhere('SUB.datetime_value', 'like', '%' . $search . '%')
                //        ->orWhere('SUB.date_value', 'like', '%' . $search . '%')
                //        ->orWhere('SUB.json_value', 'like', '%' . $search . '%');

                foreach ($modelFillableFields as $field) {
                    $query->orWhere($field, 'like', '%' . $search . '%');
                }
            });
        }

        return $query;
    }


    /**
     * @param Builder $query
     * @param array $params
     * @param array $availableFieldsGroupByCodes
     * @return Builder
     * @throws CustomErrorException
     */
    private function applyAgGridFilter(Builder $query, array $params, array $availableFieldsGroupByCodes): Builder
    {
        if (!isset($params['filters'])) {
            return $query;
        }

        $filterData = json_decode(base64_decode($params['filters']), true);

        if (empty($filterData)) {
            throw new CustomErrorException('Cant decode custom filters', 422);
        }


        foreach ($filterData as $filter) {
            $query->whereHas(
                'customFieldValues.customFieldValue',
                function ($query) use ($filter, $params, $availableFieldsGroupByCodes) {
                    $customField = $availableFieldsGroupByCodes[$filter['field']];

                    $fieldName = CustomField::$attributeTypeFields[$customField['type']];
                    $filter['field'] = $fieldName;

                    $query->where(function ($query) use ($filter, $customField) {
                        $query->where(function ($query) use ($filter, $customField) {
                            $this->attachAgGridCondition(
                                $query,
                                $filter['field'],
                                $filter['conditions'][0]['type'],
                                $filter['conditions'][0]['value'],
                                $customField['id'],
                            );
                        });

                        if (isset($filter['conditions'][1])) {
                            if ($filter['conditions'][1]['comparison'] === 'OR') {
                                $query->orWhere(function ($query) use ($filter, $customField) {
                                    $this->attachAgGridCondition(
                                        $query,
                                        $filter['field'],
                                        $filter['conditions'][1]['type'],
                                        $filter['conditions'][1]['value'],
                                        $customField['id'],
                                    );
                                });
                            } else {
                                $query->where(function ($query) use ($filter, $customField) {
                                    $this->attachAgGridCondition(
                                        $query,
                                        $filter['field'],
                                        $filter['conditions'][1]['type'],
                                        $filter['conditions'][1]['value'],
                                        $customField['id'],
                                    );
                                });
                            }
                        }
                    });
                },
            );
        }

        return $query;
    }

    private function attachAgGridCondition(
        Builder $query,
        string $field,
        string $conditionType,
        string $conditionValue,
        int $customFieldId,
    ): Builder {
        switch ($conditionType) {
            case 'contains':
                $query->where(
                    $field,
                    'LIKE',
                    '%' . $conditionValue . '%',
                )->where(
                    'field_id',
                    $customFieldId,
                );
                break;

            case 'not_contains':
                $query->where(
                    $field,
                    'NOT LIKE',
                    '%' . $conditionValue . '%',
                )->where(
                    'field_id',
                    $customFieldId,
                );
                break;
            case 'equals':
                $query->where($field, $conditionValue)->where(
                    'field_id',
                    $customFieldId,
                );
                break;
            case 'not_equal':
                $query->where($field, '!=', $conditionValue)->where(
                    'field_id',
                    $customFieldId,
                );
                break;
            case 'starts_with':
                $query->where($field, 'LIKE', $conditionValue . '%')->where(
                    'field_id',
                    $customFieldId,
                );
                break;
            case 'ends_with':
                $query->where($field, 'LIKE', '%' . $conditionValue)->where(
                    'field_id',
                    $customFieldId,
                );
                break;
            case 'blank':
                $query->whereNull($field)->orWhere($field, '')->where(
                    'field_id',
                    $customFieldId,
                );
                break;
            case 'not_blank':
                $query->whereNotNull($field)->where($field, '!=', '')->where(
                    'field_id',
                    $customFieldId,
                );
                break;

            case '>':
            case '<':
            case '<=':
            case '>=':
                $query->whereNotNull($field)->where($field, $conditionType, $conditionValue)->where(
                    'field_id',
                    $customFieldId,
                );
                break;
        }

        return $query;
    }
}
