<?php

namespace App\Helpers;

use App\Exceptions\CustomErrorException;
use App\Http\Repositories\CustomField\CustomFieldValueRepository;
use App\Models\BaseModelWithCustomFields;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CustomFieldValuesHelper
{
    public static function getCustomFieldValues(
        BaseModelWithCustomFields $model,
        array $customFieldCodeList = [],
    ): array {
        $customFieldValues = $model->customFields()->with(['customField']);
        if (!empty($customFieldCodeList)) {
            $customFieldValues = $customFieldValues->whereHas(
                'customField',
                function ($query) use ($customFieldCodeList) {
                    $query->whereIn('code', $customFieldCodeList);
                },
            );
        }

        return $customFieldValues->get()->mapWithKeys(function ($item) {
            if ($item->customField) {
                return [$item->customField->code => $item->text_value];
            }

            return [];
        })->toArray();
    }

    public static function getProductPriceValue(Product $product)
    {
        return Cache::remember(
            'productPrice#' . $product->getKey(),
            3600,
            function () use ($product) {
                $productCustomFields = CustomFieldValuesHelper::getCustomFieldValues(
                    $product,
                    ['product-price'],
                );

                if (!isset($productCustomFields['product-price'])) {
                    throw new CustomErrorException(
                        'Product with id ' . $product->getKey() . ' dont have price entered',
                        422,
                    );
                }

                return $productCustomFields['product-price'];
            },
        );
    }

    /**
     * @param string $customFieldCode
     * @param mixed $value
     * @param int $entityId
     * @param string $entityType
     * @return void
     */
    public static function insertCustomFieldValue(
        string $customFieldCode,
        mixed $value,
        int $entityId,
        string $entityType,
        User $user,
    ): void {
        /** @var CustomFieldValueRepository $repository */
        $repository = resolve(CustomFieldValueRepository::class);
        $data = [
            'updated_by' => $user->getKey(),
            'entity_type' => $entityType,
            'customFields' => [
                $customFieldCode => $value,
            ],
        ];

        $repository->saveCustomField($data, $entityId, ['code' => $customFieldCode]);
    }
}
