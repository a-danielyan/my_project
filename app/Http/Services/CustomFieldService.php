<?php

namespace App\Http\Services;

use App\Http\Repositories\CustomField\CustomFieldOptionRepository;
use App\Http\Repositories\CustomFieldRepository;
use App\Http\Resource\CustomFieldResource;
use App\Models\CustomField;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomFieldService extends BaseService
{
    private CustomFieldOptionRepository $customFieldOptionRepository;

    public function __construct(
        CustomFieldRepository $customFieldRepository,
        CustomFieldOptionRepository $customFieldOptionRepository,
    ) {
        $this->repository = $customFieldRepository;
        $this->customFieldOptionRepository = $customFieldOptionRepository;
    }

    public function resource(): string
    {
        return CustomFieldResource::class;
    }


    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->getAll($params, $user));
    }

    protected function beforeStore(array $data, Authenticatable|User $user): array
    {
        $data['code'] = Str::slug($data['name']);
        $data['created_by'] = $user->getKey();

        return $data;
    }


    protected function beforeUpdate(array $data, Model $model, Authenticatable|User $user): array
    {
        $data['code'] = Str::slug($data['name']);

        return $data;
    }

    public function bulkUpdate(array $params, Authenticatable|User $user): void
    {
        $entityType = 'App\Models\\' . $params['entity_type'];
        DB::beginTransaction();

        $this->repository->deleteAllByType($entityType, $user);

        $this->saveCustomFields($entityType, $user, $params['fields'], null);

        DB::commit();
    }

    private function saveCustomFields(string $entityType, User $author, array $customFields, ?int $parentId): void
    {
        foreach ($customFields as $field) {
            if (isset($field['id'])) {
                $updateParams = ['id' => $field['id'], 'entity_type' => $entityType];
            } else {
                $updateParams = ['name' => $field['name'], 'entity_type' => $entityType];
            }
            /** @var CustomField $customField */
            $customFieldData = [
                'entity_type' => $entityType,
                'code' => Str::slug($field['name']),
                'name' => $field['name'],
                'type' => $field['type'],
                'lookup_type' => $field['lookupType'] ?? null,
                'sort_order' => $field['sortOrder'],
                'is_required' => $field['isRequired'] ?? false,
                'is_unique' => $field['isUnique'] ?? false,
                'is_multiple' => $field['isMultiple'] ?? false,
                'parent_id' => $parentId,
                'updated_by' => $author->getKey(),
                'width' => $field['width'] ?? 1,
                'deleted_at' => null,
                'tooltip' => $field['tooltip'] ?? null,
                'tooltip_type' => $field['tooltipType'] ?? 'icon',
                'property' => $field['property'] ?? null,
            ];

            $customField = $this->repository->first(where: $updateParams, isTrashed: true);
            if ($customField) {
                $this->repository->update($customField, Arr::except($customFieldData, ['code']));
            } else {
                $customField = $this->repository->create($customFieldData);
            }

            $options = $field['options'] ?? [];

            if (
                in_array(
                    $field['type'],
                    [
                        CustomField::FIELD_TYPE_SELECT,
                        CustomField::FIELD_TYPE_MULTISELECT,
                        CustomField::FIELD_TYPE_CHECKBOX,
                    ],
                ) && count($options)
            ) {
                $this->customFieldOptionRepository->deleteByParams(['custom_field_id' => $customField->getKey()]);

                $sortOrder = 1;

                foreach ($options as $optionInput) {
                    $this->customFieldOptionRepository->create(
                        [
                            'custom_field_id' => $customField->getKey(),
                            'sort_order' => $sortOrder++,
                            'name' => $optionInput,
                        ],
                    );
                }
            }


            if ($customField->wasRecentlyCreated) {
                $customField->created_by = $author->getKey();
                $customField->save();
            }

            $this->saveCustomFields($entityType, $author, $field['childs'] ?? [], $customField->getKey());
        }
    }
}
