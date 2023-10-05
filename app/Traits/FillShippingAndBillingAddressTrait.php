<?php

namespace App\Traits;

use App\Helpers\CustomFieldValuesHelper;
use App\Models\BaseModelWithCustomFields;

trait FillShippingAndBillingAddressTrait
{
    private function convertSavedAddressArray(
        BaseModelWithCustomFields $model,
        array &$savedBillingAddress,
        array &$savedShippingAddress,
    ): void {
        $contactAddresses = CustomFieldValuesHelper::getCustomFieldValues($model, ['addresses']);
        if (!empty($contactAddresses['addresses'])) {
            $savedBillingAddress = (array)$contactAddresses['addresses'][0];
            $savedShippingAddress = (array)$contactAddresses['addresses'][0];

            foreach ($contactAddresses['addresses'] as $address) {
                if (isset($address->isBilling) && $address->isBilling === true) {
                    $savedBillingAddress = (array)$address;
                }
                if (isset($address->isShipping) && $address->isShipping === true) {
                    $savedShippingAddress = (array)$address;
                }
            }
        }
    }

    private function fillSavedBillingAndShippingAddresses(
        array $data,
        array $savedBillingAddress,
        array $savedShippingAddress,
    ): array {
        if (!empty($savedBillingAddress) || !empty($savedShippingAddress)) {
            $billingAddressFieldsForEstimateToContactRelation = [
                'billing-street' => 'address1',
                'billing-city' => 'city',
                'billing-state' => 'stateShort',
                'billing-country' => 'country',
            ];

            $shippingAddressFieldsForEstimateToContactRelation = [
                'shipping-street' => 'address1',
                'shipping-city' => 'city',
                'shipping-state' => 'stateShort',
                'shipping-country' => 'country',
            ];

            foreach ($billingAddressFieldsForEstimateToContactRelation as $key => $value) {
                if (empty($data['customFields'][$key])) {
                    if (!empty($savedBillingAddress[$value])) {
                        $data['customFields'][$key] = $savedBillingAddress[$value];
                    }
                }
            }

            foreach ($shippingAddressFieldsForEstimateToContactRelation as $key => $value) {
                if (empty($data['customFields'][$key])) {
                    if (!empty($savedShippingAddress[$value])) {
                        $data['customFields'][$key] = $savedShippingAddress[$value];
                    }
                }
            }
        }

        return $data;
    }
}
