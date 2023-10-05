<?php

namespace App\Http\Services\ZohoNotificationHandler;

use App\Exceptions\CustomErrorException;
use App\Exceptions\InvalidMappingErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Helpers\CommonHelper;
use App\Helpers\ZohoImport\ZohoMapperFactory;
use App\Helpers\ZohoImport\ZohoMapperInterface;
use App\Http\Repositories\BaseRepositoryWithCustomFields;
use App\Http\Services\BaseService;
use App\Http\Services\ZohoConverterService;
use App\Models\CustomField;

abstract class BaseZohoNotificationHandler implements ZohoNotificationHandlerInterface
{
    public function __construct(private BaseService $service, private BaseRepositoryWithCustomFields $repository)
    {
    }

    /**
     * @param string $zohoEntityId
     * @param array $data
     * @return void
     * @throws CustomErrorException
     * @throws ModelUpdateErrorException
     */
    public function updateEntity(string $zohoEntityId, array $data): void
    {
        unset($data['id']);
        $model = $this->repository->firstOrFail(where: ['zoho_entity_id' => $zohoEntityId]);

        $zohoMapper = ZohoMapperFactory::getMapperForEntity($this->moduleName());

        $customFieldsForUpdate = [];

        $customFields = CustomField::query()->where('entity_type', $zohoMapper->getEntityClassName())
            ->where('type', '!=', 'container')
            ->get()->keyBy('code');

        foreach ($customFields as $customField) {
            try {
                $zohoValue = $zohoMapper->mapZohoFieldsToOurCustomFields(
                    $customField,
                    $data,
                );
                $customFieldsForUpdate[$customField->code] = $zohoValue;
            } catch (InvalidMappingErrorException) {
                continue;
            }
        }

        $dataForUpdate = ['entity_type' => $zohoMapper->getEntityClassName(), 'customFields' => $customFieldsForUpdate];

        $this->service->update($dataForUpdate, $model, CommonHelper::getCronUser());
    }

    public function createEntity(array $data): void
    {
        $customFields = CustomField::query();
        /** @var ZohoMapperInterface $zohoConverter */
        try {
            $zohoConverter = ZohoMapperFactory::getMapperForEntity($this->moduleName());
        } catch (CustomErrorException) {
            return;
        }

        $customFields = $customFields->where('entity_type', $zohoConverter->getEntityClassName())
            ->where('type', '!=', 'container')
            ->get()->keyBy('code');

        $zohoData = $data;
        $zohoData['Id'] = $data['id'];

        $zohoConverterService = new ZohoConverterService($zohoConverter, $customFields);

        $zohoConverterService->insertZohoEntityToOurDatabase($zohoData);
    }

    abstract public function moduleName(): string;
}
