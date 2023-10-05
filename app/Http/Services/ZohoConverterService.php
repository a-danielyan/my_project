<?php

namespace App\Http\Services;

use App\Exceptions\InvalidMappingErrorException;
use App\Helpers\ZohoImport\ZohoMapperInterface;
use App\Http\Repositories\TagRepository;
use App\Models\CustomField;
use App\Models\CustomFieldValues;
use App\Models\TagEntityAssociation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class ZohoConverterService
{
    public function __construct(private ZohoMapperInterface $zohoConverter, private Collection $customFields)
    {
    }

    public function insertZohoEntityToOurDatabase(array $zohoData): void
    {
        $ourEntity = $this->zohoConverter->getRepository()->updateOrCreate(
            ['zoho_entity_id' => $zohoData['Id']],
            $this->zohoConverter->getInternalFields($zohoData),
        );

        CustomFieldValues::query()->where('entity', $this->zohoConverter->getEntityClassName())->where(
            'entity_id',
            $ourEntity->id,
        )->delete();

        $zohoData = $this->zohoConverter->beforeMap($zohoData);
        foreach ($this->customFields as $customField) {
            try {
                $zohoValue = $this->zohoConverter->mapZohoFieldsToOurCustomFields(
                    $customField,
                    $zohoData,
                );
            } catch (InvalidMappingErrorException) {
                continue;
            }
            $createdData = array_merge([
                'field_id' => $customField->getKey(),
                'entity_id' => $ourEntity->getKey(),
                'entity' => $this->zohoConverter->getEntityClassName(),
            ], $this->getPropertyValue($customField, $zohoValue));

            CustomFieldValues::query()->create(
                $createdData,
            );
        }
        if (is_array($zohoData['Tag'])) {
            foreach ($zohoData['Tag'] as $tag) {
                $this->assignTagToEntity($tag, $ourEntity);
            }
        } else {
            $this->assignTagToEntity($zohoData['Tag'] ?? '', $ourEntity);
        }

        $this->zohoConverter->afterInserted($ourEntity, $zohoData);
    }


    /**
     * @param CustomField $customField
     * @param string|int $value
     * @return array
     */
    private function getPropertyValue(CustomField $customField, string|int $value): array
    {
        return match ($customField->type) {
            'email', 'phone', 'text' => ['text_value' => $value],
            'lookup', 'select' => ['integer_value' => $value],
            'multiselect' => ['text_value' => $value . ','],
            'number' => ['float_value' => (float)$value],
            default => [],
        };
    }


    private function assignTagToEntity(string $zohoTag, Model $ourEntity): void
    {
        if (empty($zohoTag)) {
            return;
        }
        /** @var TagRepository $tagRepository */
        $tagRepository = resolve(TagRepository::class);

        $tag = $tagRepository->firstOrCreate(
            ['entity_type' => $this->zohoConverter->getEntityClassName(), 'tag' => $zohoTag],
            [
                'entity_type' => $this->zohoConverter->getEntityClassName(),
                'tag' => $zohoTag,
                'created_by' => 1,
            ],
        );
        try {
            TagEntityAssociation::query()->create([
                'tag_id' => $tag->getKey(),
                'entity' => $this->zohoConverter->getEntityClassName(),
                'entity_id' => $ourEntity->getKey(),
            ]);
        } catch (Throwable) {
            return;
        }
    }
}
