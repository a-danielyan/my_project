<?php

namespace App\Http\Services\ZohoNotificationHandler;

use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Helpers\CommonHelper;
use App\Helpers\ZohoImport\ZohoMapperFactory;
use App\Http\Repositories\ActivityRepository;
use App\Http\Services\ActivityService;

class ActivityZohoNotificationHandler implements ZohoNotificationHandlerInterface
{
    public function __construct(private ActivityService $service, private ActivityRepository $repository)
    {
    }

    public function moduleName(): string
    {
        return 'Tasks';
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

        $dataForUpdate = $zohoMapper->getInternalFields($data);

        $this->service->update($dataForUpdate, $model, CommonHelper::getCronUser());
    }

    public function createEntity(array $data)
    {
        // TODO: Implement createEntity() method.
    }
}
