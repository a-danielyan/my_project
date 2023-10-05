<?php

namespace App\Traits;

use App\Exceptions\CustomErrorException;
use App\Helpers\CustomFieldValuesHelper;
use App\Models\Contact;
use App\Models\CustomField;
use App\Models\CustomFieldValues;
use App\Models\Interfaces\ModelWithContactInterface;
use App\Models\Product;

trait TaxableModelTotals
{
    /**
     * @param array $data
     * @param ModelWithContactInterface $model
     * @return void
     * @throws CustomErrorException
     */
    private function updateModelTotals(array $data, ModelWithContactInterface $model): void
    {
        $subTotal = 0;
        $totalDiscount = 0;

        $account = $model->account;
        if (empty($account)) {
            throw new CustomErrorException('We dont have account');
        }
        $contact = $model->contact;
        if (empty($contact)) {
            throw new CustomErrorException('We dont have contact');
        }

        $stateCustomField = CustomField::query()->where('entity_type', Contact::class)
            ->where('code', 'contact-state')->first();
        /** @var CustomFieldValues $contactStateValue */
        $contactStateValue = CustomFieldValues::query()->where('entity_id', $contact->getKey())
            ->where('field_id', $stateCustomField->getKey())
            ->first();


        $contactState = '';
        if ($contactStateValue) {
            $contactState = $contactStateValue->text_value;
        }

        $totalTax = 0;

        foreach ($data['itemGroups'] as $itemGroup) {
            foreach ($itemGroup['items'] as $productItem) {
                /** @var Product $product */
                $product = $this->productRepository->findById($productItem['productId']);
                if (!$product) {
                    throw new CustomErrorException(
                        'Product with id ' . $productItem['productId'] . ' not found',
                        422,
                    );
                }
                $productPrice = CustomFieldValuesHelper::getProductPriceValue($product);
                $itemSubTotal = $productItem['quantity'] * $productPrice;
                //    $linePrice = $itemSubTotal - $productItem['discount'];
                //    $subTotal += $linePrice;
                $subTotal += $itemSubTotal;

                $taxPercent = $productItem['taxPercent'] ?? 0;
                $lineTax = ($itemSubTotal * $taxPercent) / 100;
                $totalTax += $lineTax;

                $totalDiscount += $productItem['discount'];
            }
        }

        if (count($data['itemGroups']) == 1) {
            $totalTax = $this->calculateTax($subTotal, $contactState, $account->getKey());
        }

        if ($totalDiscount > $subTotal) {
            throw new CustomErrorException('Discount cant be more that subtotal', 422);
        }
        $grandTotal = $subTotal + $totalTax - $totalDiscount;

        if (!empty($data['discount_percent'])) {
            $additionalDiscount = round((($subTotal - $totalDiscount) * $data['discount_percent']) / 100, 2);
            $grandTotal = $grandTotal - $additionalDiscount;
        }


        $model->sub_total = round($subTotal, 2);
        $model->total_tax = round($totalTax, 2);
        $model->grand_total = round($grandTotal, 2);
        $model->total_discount = round($totalDiscount, 2);
        $model->discount_percent = $data['discount_percent'] ?? 0;
        $model->tax_percent = $data['tax_percent'] ?? 0;
        $model->save();
    }
}
