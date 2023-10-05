<?php

namespace App\Http\Services;

use App\Exceptions\CustomErrorException;
use App\Helpers\StorageHelper;
use App\Http\Repositories\TemplateRepository;
use App\Http\Resource\TemplateResource;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

class TemplateService extends BaseService
{
    public function __construct(
        TemplateRepository $templateRepository,
    ) {
        $this->repository = $templateRepository;
    }

    public function resource(): string
    {
        return TemplateResource::class;
    }

    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->getAllSharedTemplates($params, $user));
    }

    /**
     * @param array $data
     * @param Model $model
     * @param Authenticatable|User $user
     * @return array
     * @throws CustomErrorException
     */
    protected function beforeUpdate(array $data, Model $model, Authenticatable|User $user): array
    {
        $data['entity_type'] = Template::class;
        $data['updated_by'] = $user->getKey();

        if (isset($data['thumbImage'])) {
            try {
                $savePath = '/template/' . $model->getKey() . '/thumb';
                $savedFile = StorageHelper::storeFile($data['thumbImage'], $savePath);
                $data['thumb_image'] = $savedFile;
            } catch (Throwable $e) {
                throw new CustomErrorException($e->getMessage(), 422);
            }
        }


        return $data;
    }

    protected function beforeStore(array $data, Authenticatable|User $user): array
    {
        $data = parent::beforeStore($data, $user);
        $data['entity_type'] = Template::class;

        if (in_array($data['entity'], [Template::TEMPLATE_TYPE_PROPOSAL, Template::TEMPLATE_TYPE_INVOICE])) {
            $data['is_shared'] = true;
        }

        return $data;
    }

    public function show(Model $model, string $resource = null): JsonResource
    {
        $model = $model->load('tag');

        return parent::show($model, $resource);
    }


    /**
     * @param Model $model
     * @param array $data
     * @param Authenticatable|User $user
     * @return void
     * @throws CustomErrorException
     */
    protected function afterStore(Model $model, array $data, Authenticatable|User $user): void
    {
        /** @var Template $model */
        if (isset($data['thumbImage'])) {
            try {
                $savePath = '/template/' . $model->getKey() . '/thumb';
                $savedFile = StorageHelper::storeFile($data['thumbImage'], $savePath);
                $model->thumb_image = $savedFile;
                $model->save();
            } catch (Throwable $e) {
                throw new CustomErrorException($e->getMessage(), 422);
            }
        }

        parent::afterStore($model, $data, $user);
    }
}
