<?php

namespace App\Http\Repositories;

use App\Models\EstimateItem;
use App\Models\EstimateShippingGroupItems;

class EstimateItemRepository extends BaseRepository
{
    /**
     * @param EstimateItem $estimateItem
     */
    public function __construct(EstimateItem $estimateItem)
    {
        $this->model = $estimateItem;
    }


    public function saveEstimateItems(array $data, int $estimateId): void
    {
        EstimateShippingGroupItems::query()->where('estimate_id', $estimateId)->delete();

        foreach ($data as $groupItems) {
            $groupItem = EstimateShippingGroupItems::query()->create([
                'contact_id' => $groupItems['contactId'] ?? null,
                'estimate_id' => $estimateId,
                'address' => $groupItems['address'] ?? null,
            ]);

            $this->createItem($groupItems['items'], $groupItem->getKey(), null);
        }
    }

    private function createItem(array $itemsList, int $groupItemId, ?int $parentId): void
    {
        foreach ($itemsList as $productItem) {
            $item = $this->create([
                'group_id' => $groupItemId,
                'product_id' => $productItem['productId'],
                'quantity' => $productItem['quantity'],
                'discount' => $productItem['discount'] ?? 0,
                'description' => $productItem['description'] ?? '',
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
