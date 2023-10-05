<?php

namespace App\Http\Repositories;

use App\Exceptions\CustomErrorException;
use App\Helpers\CustomFieldValuesHelper;
use App\Models\InvoiceItem;
use App\Models\InvoiceShippingGroupItem;
use App\Models\Product;

class InvoiceItemRepository extends BaseRepository
{
    /**
     * @param InvoiceItem $invoiceItem
     * @param ProductRepository $productRepository
     */
    public function __construct(InvoiceItem $invoiceItem, private ProductRepository $productRepository)
    {
        $this->model = $invoiceItem;
    }

    /**
     * @param array $data
     * @param int $invoiceId
     * @return void
     * @throws CustomErrorException
     */
    public function saveInvoiceItems(array $data, int $invoiceId): void
    {
        InvoiceShippingGroupItem::query()->where('invoice_id', $invoiceId)->delete();

        foreach ($data as $groupItems) {
            $groupItem = InvoiceShippingGroupItem::query()->create([
                'contact_id' => $groupItems['contactId'] ?? null,
                'invoice_id' => $invoiceId,
                'address' => $groupItems['address'] ?? null,
            ]);

            $this->createItem($groupItems['items'], $groupItem->getKey(), null);
        }
    }

    /**
     * @param array $itemsList
     * @param int $groupItemId
     * @param int|null $parentId
     * @return void
     * @throws CustomErrorException
     */
    private function createItem(array $itemsList, int $groupItemId, ?int $parentId): void
    {
        foreach ($itemsList as $productItem) {
            /** @var Product $product */
            $product = $this->productRepository->findById($productItem['productId']);
            if (!$product) {
                throw new CustomErrorException(
                    'Product with id ' . $productItem['productId'] . ' not found',
                    422,
                );
            }
            $productPrice = CustomFieldValuesHelper::getProductPriceValue($product);

            $item = $this->create([
                'group_id' => $groupItemId,
                'product_id' => $productItem['productId'],
                'quantity' => $productItem['quantity'],
                'discount' => $productItem['discount'] ?? 0,
                'total' => $productPrice * $productItem['quantity'],
                'subtotal' => $productPrice * $productItem['quantity'],
                'tax' => 0,
                //           'description' => $productItem['description'] ?? '',
                'tax_percent' => $productItem['taxPercent'] ?? 0,
                'parent_id' => $parentId,
                'combine_price' => $productItem['combinePrice'] ?? false,
            ]);
            if (!empty($productItem['childItems'])) {
                $this->createItem($productItem['childItems'], $groupItemId, $item->getKey());
            }
        }
    }
}
