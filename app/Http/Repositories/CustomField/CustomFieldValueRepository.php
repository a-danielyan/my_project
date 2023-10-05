<?php

namespace App\Http\Repositories\CustomField;

use App\Events\ModelChanged;
use App\Http\Repositories\BaseRepository;
use App\Http\Repositories\EntityLogRepository;
use App\Models\CustomField;
use App\Models\CustomFieldValues;

class CustomFieldValueRepository extends BaseRepository
{
    protected CustomFieldRepository $customFieldRepository;
    protected EntityLogRepository $entityLogRepository;

    public function __construct(
        CustomFieldRepository $customFieldRepository,
        CustomFieldValues $customFieldValues,
        EntityLogRepository $entityLogRepository,
    ) {
        $this->customFieldRepository = $customFieldRepository;
        $this->model = $customFieldValues;
        $this->entityLogRepository = $entityLogRepository;
    }

    public function saveCustomField(array $data, $entityId, array $customFieldConditionFilter = []): void
    {
        if (!isset($data['entity_type']) || !isset($data['customFields'])) {
            return;
        }
        $conditions = array_merge(['entity_type' => $data['entity_type']], $customFieldConditionFilter);

        $attributes = $this->customFieldRepository->get(where: $conditions);

        $customFieldsData = $data['customFields'];
        if (empty($this->updateId)) {
            $this->updateId = time();
        }

        $changedEntityLog = [];
        foreach ($attributes as $attribute) {
            if (
                $attribute->type == CustomField::FIELD_TYPE_CONTAINER ||
                $attribute->type == CustomField::FIELD_TYPE_INTERNAL
            ) {
                continue;
            }
            $typeColumn = CustomField::$attributeTypeFields[$attribute->type];
            if ($attribute->is_multiple) {
                $typeColumn = 'json_value';
            }

            if ($attribute->type === CustomField::FIELD_TYPE_BOOL) {
                $customFieldsData[$attribute->code] = isset($customFieldsData[$attribute->code]) &&
                $customFieldsData[$attribute->code] ? 1 : 0;
            }

            if (!array_key_exists($attribute->code, $customFieldsData)) {
                continue;
            }

            if ($attribute->type === CustomField::FIELD_TYPE_DATE && $customFieldsData[$attribute->code] === '') {
                $customFieldsData[$attribute->code] = null;
            }

            if ($attribute->type === CustomField::FIELD_TYPE_MULTISELECT) {
                $customFieldsData[$attribute->code] = implode(',', $customFieldsData[$attribute->code]);
            }

            if (
                $attribute->type === CustomField::FIELD_TYPE_IMAGE ||
                $attribute->type === CustomField::FIELD_TYPE_FILE
            ) {
                $customFieldsData[$attribute->code] = gettype($customFieldsData[$attribute->code]) === 'object'
                    ? request()->file($attribute->code)->store($customFieldsData['entity_type'] . '/' . $entityId)
                    : null;
            }

            $attributeValue = $this->get(where: [
                'entity_id' => $entityId,
                'field_id' => $attribute->id,
            ])->first();

            if (!$attributeValue) {
                $this->create([
                    'entity_id' => $entityId,
                    'entity' => $data['entity_type'],
                    'field_id' => $attribute->id,
                    $typeColumn => $customFieldsData[$attribute->code],
                ]);
            } else {
                if ($attributeValue[$typeColumn] !== $customFieldsData[$attribute->code]) {
                    $oldValue = $attributeValue[$typeColumn] ?? '';
                    if (is_array($oldValue)) {
                        $oldValue = json_encode($oldValue);
                    }
                    $newValue = $customFieldsData[$attribute->code];
                    if (is_array($newValue)) {
                        $newValue = json_encode($newValue);
                    }

                    $this->update($attributeValue, [
                        $typeColumn => $customFieldsData[$attribute->code],
                    ]);

                    $changedEntityLog[] = [
                        'entity' => $data['entity_type'],
                        'entity_id' => $entityId,
                        'field_id' => $attribute->id,
                        'previous_value' => $oldValue,
                        'new_value' => $newValue,
                        'updated_by' => $data['updated_by'],
                        'update_id' => $this->updateId,
                        'created_at' => now(),
                    ];
                }
                /*  if ($attribute->type == 'image' || $attribute->type == 'file') {
                      Storage::delete($attributeValue->text_value);
                  }*/
            }
        }
        ModelChanged::dispatch($changedEntityLog);
    }

    public function isValueUnique($entityId, $entityType, $attribute, $value): bool
    {
        $query = $this->model->newQuery()
            ->where('attribute_id', $attribute->id)
            ->where('entity_type', $entityType)
            ->where('entity_id', '!=', $entityId);

        if (in_array($attribute->type, ['email', 'phone'])) {
            $query->where(CustomField::$attributeTypeFields[$attribute->type], 'like', '%' . $value . '%');
        } else {
            $query->where(CustomField::$attributeTypeFields[$attribute->type], $value);
        }

        return !$query->get()->count();
    }

    public function getCustomFieldValue(string $entity, int $entityId, string $customFieldCode): CustomFieldValues
    {
        /** @var CustomFieldValues $customFieldValue */
        $customFieldValue = $this->model->newQuery()->where('entity', $entity)
            ->where('entity_id', $entityId)->whereHas('customField', function ($query) use ($entity, $customFieldCode) {
                $query->where('entity_type', $entity)->where('code', $customFieldCode);
            })->first();

        return $customFieldValue;
    }
}
