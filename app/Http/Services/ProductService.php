<?php

namespace App\Http\Services;

use App\Exceptions\CustomErrorException;
use App\Helpers\StorageHelper;
use App\Http\Repositories\ProductAttachmentRepository;
use App\Http\Repositories\ProductRepository;
use App\Http\Resource\ProductResource;
use App\Models\Product;
use App\Models\ProductAttachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProductService extends BaseService
{
    public function __construct(
        ProductRepository $productRepository,
        private ProductAttachmentRepository $productAttachmentRepository,
    ) {
        $this->repository = $productRepository;
    }

    public function resource(): string
    {
        return ProductResource::class;
    }

    /**
     * @param array $params
     * @param Authenticatable|User $user
     * @return array
     * @throws CustomErrorException
     * @throws ValidationException
     */
    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate(
            $this->repository->getAllWithCustomFields($params, $user, [
                'customFields',
                'customFields.customField',
            ]),
        );
    }

    protected function beforeStore(array $data, Authenticatable|User $user): array
    {
        $data['created_by'] = $user->getKey();
        $data['entity_type'] = Product::class;

        return $data;
    }

    protected function beforeUpdate(array $data, Model $model, Authenticatable|User $user): array
    {
        $data['updated_by'] = $user->getKey();
        $data['entity_type'] = Product::class;

        return $data;
    }

    public function show(Model $model, string $resource = null): JsonResource
    {
        $model = $model->load(
            [
                'tag',
                'customFields',
                'attachments',
            ],
        );

        return parent::show($model, $resource);
    }

    /**
     * @param array $data
     * @param Product $product
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function storeAttachment(array $data, Product $product, User|Authenticatable $user): void
    {
        if (isset($data['link'])) {
            $this->productAttachmentRepository->create([
                'product_id' => $product->getKey(),
                'name' => $data['name'] ?? '',
                'attachment_link' => $data['link'],
                'created_by' => $user->getKey(),
            ]);
        } else {
            if (isset($data['file'])) {
                try {
                    $savePath = '/product/' . $product->getKey() . '/attachments';
                    $savedFile = StorageHelper::storeFile($data['file'], $savePath);

                    $this->productAttachmentRepository->create([
                        'product_id' => $product->getKey(),
                        'attachment_file' => $savedFile,
                        'name' => $data['name'] ?? '',
                        'created_by' => $user->getKey(),
                    ]);
                    //we can get later  file with StorageHelper::getSignedStorageUrl($filename, 's3');
                } catch (Throwable $e) {
                    throw new CustomErrorException($e->getMessage(), 422);
                }
            }
        }
    }

    /**
     * @param array $data
     * @param Product $product
     * @param ProductAttachment $attachment
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function updateAttachment(
        array $data,
        Product $product,
        ProductAttachment $attachment,
        User|Authenticatable $user,
    ): void {
        if (isset($data['link'])) {
            $this->productAttachmentRepository->update($attachment, [
                'attachment_link' => $data['link'],
                'attachment_file' => null,
                'name' => $data['name'] ?? '',
                'updated_by' => $user->getKey(),
            ]);
        } else {
            if (isset($data['file'])) {
                try {
                    $savePath = '/product/' . $product->getKey() . '/attachments';
                    $savedFile = StorageHelper::storeFile($data['file'], $savePath);

                    $this->productAttachmentRepository->update($attachment, [
                        'attachment_file' => $savedFile,
                        'attachment_link' => null,
                        'name' => $data['name'] ?? '',
                        'updated_by' => $user->getKey(),
                    ]);
                    //we can get later  file with StorageHelper::getSignedStorageUrl($filename, 's3');
                } catch (Throwable $e) {
                    throw new CustomErrorException($e->getMessage(), 422);
                }
            }
        }
    }

    public function deleteAttachment(ProductAttachment $attachment): void
    {
        $this->productAttachmentRepository->delete($attachment);
    }

    protected function afterUpdate(Model $model, array $data, Authenticatable|User $user): void
    {
        Cache::forget('productPrice#' . $model->getKey());
    }
}
