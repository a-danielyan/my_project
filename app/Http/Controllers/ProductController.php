<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelDeleteErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Http\Requests\Product\ProductBulkDeleteRequest;
use App\Http\Requests\Product\ProductBulkUpdateRequest;
use App\Http\Requests\Product\ProductGetRequest;
use App\Http\Requests\Product\ProductStoreAttachmentRequest;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\RequestTransformers\Product\ProductBulkUpdateTransformer;
use App\Http\RequestTransformers\Product\ProductStoreTransformer;
use App\Http\RequestTransformers\Product\ProductGetSortTransformer;
use App\Http\Services\ProductService;
use App\Models\Product;
use App\Models\ProductAttachment;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function __construct(private ProductService $service)
    {
        $this->authorizeResource(Product::class, 'product');
    }

    protected function resourceMethodsWithoutModels(): array
    {
        return array_merge(
            parent::resourceMethodsWithoutModels(),
            ['bulkUpdate'],
        );
    }

    /**
     * @param ProductGetRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     * @throws ValidationException
     */
    public function index(ProductGetRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getAll(
                (new ProductGetSortTransformer())->map($request->all()),
                $this->getUser(),
            ),
        );
    }

    /**
     * @param ProductStoreRequest $request
     * @return JsonResponse
     * @throws ModelCreateErrorException
     */
    public function store(ProductStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->store((new ProductStoreTransformer())->transform($request), $this->getUser()),
        );
    }

    /**
     * @param Product $product
     * @return JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json($this->service->show($product));
    }

    /**
     * @param ProductUpdateRequest $request
     * @param Product $product
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function update(ProductUpdateRequest $request, Product $product): JsonResponse
    {
        return response()->json(
            $this->service->update((new ProductStoreTransformer())->transform($request), $product, $this->getUser()),
        );
    }

    /**
     * @param Product $product
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->service->delete($product, $this->getUser());

        return response()->json();
    }

    /**
     * @param ProductBulkDeleteRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ModelDeleteErrorException
     */
    public function bulkDeletes(ProductBulkDeleteRequest $request): JsonResponse
    {
        $this->authorize('deleteBulk', [Product::class]);

        $this->service->bulkDelete($request->all(), $this->getUser());

        return response()->json();
    }

    /**
     * @param ProductBulkUpdateRequest $request
     * @return JsonResponse
     * @throws ModelUpdateErrorException
     */
    public function bulkUpdate(ProductBulkUpdateRequest $request): JsonResponse
    {
        $params = (new ProductBulkUpdateTransformer())->transform($request);
        $this->service->bulkUpdate($params, $this->getUser());

        return response()->json();
    }

    /**
     * @param int $productId
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws CustomErrorException
     */
    public function restoreItem(int $productId): JsonResponse
    {
        $this->authorize('restoreItem', [Product::class]);
        $this->service->restoreItem($productId, $this->getUser());

        return response()->json();
    }

    /**
     * @param Product $product
     * @param ProductStoreAttachmentRequest $request
     * @return JsonResponse
     * @throws CustomErrorException
     */
    public function addAttachment(Product $product, ProductStoreAttachmentRequest $request): JsonResponse
    {
        $this->service->storeAttachment($request->all(), $product, $this->getUser());

        return response()->json();
    }

    /**
     * @param Product $product
     * @param ProductAttachment $attachment
     * @param ProductStoreAttachmentRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws CustomErrorException
     */
    public function updateAttachment(
        Product $product,
        ProductAttachment $attachment,
        ProductStoreAttachmentRequest $request,
    ): JsonResponse {
        $this->authorize('updateAttachment', [$product, $attachment]);
        $this->service->updateAttachment($request->all(), $product, $attachment, $this->getUser());

        return response()->json();
    }

    /**
     * @param Product $product
     * @param ProductAttachment $attachment
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function deleteAttachment(Product $product, ProductAttachment $attachment): JsonResponse
    {
        $this->authorize('deleteAttachment', [$product, $attachment]);
        $this->service->deleteAttachment($attachment);

        return response()->json();
    }
}
