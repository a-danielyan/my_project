<?php

namespace App\Helpers\ZohoImport;

use App\Helpers\CommonHelper;
use App\Http\Repositories\BaseRepository;
use App\Http\Repositories\ProductRepository;
use App\Models\Product;
use App\Models\User;

class ProductsZohoMapper extends BaseZohoMapper
{
    public function getMappingValues(): array
    {
        return [

            'product-active' => 'Product_Active',
            'product-category' => 'Product_Category',
            'product-code' => 'Product_Code',
            'product-description' => 'Product_Description',
            'product-image' => 'Record_Image',
//'product-information'=>'',
            'product-name' => 'Product_Name',
            'product-price' => 'Unit_Price',
            'product-recurring' => 'Recurring_Item',
//'recurring-frequency'=>'',
            'sales-end-date' => 'Sales_End_Date',
            'support-end-date' => 'Support_Expiry_Date',
            'unit-of-measure' => 'Usage_Unit',
        ];
    }

    public function getEntityClassName(): string
    {
        return Product::class;
    }

    public function getRepository(): BaseRepository
    {
        return resolve(ProductRepository::class);
    }

    public function getInternalFields(array $zohoData, bool $isUpdate = false): array
    {
        $cronUser = CommonHelper::getCronUser();
        $internalFields = [
            'created_by' => $cronUser->getKey(),
            'zoho_entity_id' => $zohoData['Id'] ?? null,
            'status' => $this->getProductStatus($zohoData['Product_Active'] ?? null),
        ];


        if ($isUpdate) {
            $internalFields = array_filter($internalFields);
            $internalFields['updated_by'] = $cronUser->getKey();
        }

        return $internalFields;
    }

    private function getProductStatus(?string $productStatus): ?string
    {
        if (empty($productStatus)) {
            return null;
        }

        if ($productStatus === 'true') {
            return User::STATUS_ACTIVE;
        }

        return User::STATUS_INACTIVE;
    }
}
