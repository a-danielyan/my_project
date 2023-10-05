<?php

namespace App\Traits;

use App\Exceptions\ApolloRateLimitErrorException;
use App\Exceptions\CustomErrorException;
use App\Http\Repositories\CustomFieldRepository;
use App\Http\Services\ApolloIoService;
use App\Models\Account;
use App\Models\BaseModelWithCustomFields;
use App\Models\CustomField;
use App\Models\CustomFieldValues;
use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Throwable;

trait SyncDataFromApolloTrait
{
    use CustomFieldValuesConverterTrait;

    /**
     * @param BaseModelWithCustomFields $model
     * @param User|Authenticatable $user
     * @return void
     * @throws ApolloRateLimitErrorException
     * @throws CustomErrorException
     */
    public function syncPeopleDataFromApollo(
        BaseModelWithCustomFields $model,
        User|Authenticatable $user,
    ): void {
        $customFieldValues = $this->convertCustomFieldValuesToKeyValueWithId($model->customFields)->toArray();

        $recordEmail = $customFieldValues['email'];

        if (!$recordEmail) {
            throw new CustomErrorException('No email founded for this lead', 422);
        }
        /** @var ApolloIoService $apolloIoService */
        $apolloIoService = resolve(ApolloIoService::class);
        try {
            $apolloData = $apolloIoService->getPeopleByEmail($recordEmail['value']);
        } catch (ApolloRateLimitErrorException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new CustomErrorException($e->getMessage(), 422);
        }
        if (!isset($apolloData['person'])) {
            return;
        }
        $this->applyApolloDataToModel(
            $apolloData['person'],
            $model,
            $user,
            $customFieldValues,
        );
    }

    /**
     * @param Account $model
     * @param User|Authenticatable $user
     * @return void
     * @throws ApolloRateLimitErrorException
     * @throws CustomErrorException
     */
    public function syncOrganizationDataFromApollo(
        Account $model,
        User|Authenticatable $user,
    ): void {
        $customFieldValues = $this->convertCustomFieldValuesToKeyValueWithId($model->customFields)->toArray();

        $accountWebSite = $customFieldValues['website'] ?? null;

        if (!$accountWebSite) {
            throw new CustomErrorException('No website founded for this account', 422);
        }

        $parsedWebsiteData = parse_url($accountWebSite['value']);
        if (!isset($parsedWebsiteData['host'])) {
            throw new CustomErrorException('No host founded for this account', 422);
        }
        /** @var ApolloIoService $apolloIoService */
        $apolloIoService = resolve(ApolloIoService::class);
        try {
            $apolloData = $apolloIoService->getOrganizationByDomain($parsedWebsiteData['host']);
        } catch (ApolloRateLimitErrorException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new CustomErrorException($e->getMessage(), 422);
        }
        if (!isset($apolloData['organization'])) {
            return;
        }
        $this->applyApolloDataToModel(
            $apolloData['organization'],
            $model,
            $user,
            $customFieldValues,
        );
    }

    private function applyApolloDataToModel(
        array $apolloData,
        BaseModelWithCustomFields $model,
        User|Authenticatable $user,
        array $customFieldValues,
    ): void {
        $customFieldRepository = resolve(CustomFieldRepository::class);
        $allAvailableCustomFields = $customFieldRepository->getAllForEntity($model::class)->mapWithKeys(
            function ($item) {
                return [$item->code => ['id' => $item->id, 'type' => $item->type]];
            },
        )->toArray();

        $address = [
            'street_address' => $apolloData['street_address'] ?? '',
            'city' => $apolloData['city'] ?? '',
            'state' => $apolloData['state'] ?? '',
            'postal_code' => $apolloData['postal_code'] ?? '',
            'country' => $apolloData['country'] ?? '',
        ];

        $dataForUpdate = [];
        foreach ($apolloData as $field => $value) {
            if (empty($value)) {
                //check maybe we have value in contact
                $value = $apolloData['contact'][$field] ?? null;
            }

            if (!empty($value) && isset(self::APOLLO_TO_CUSTOM_FIELD_MAPPING[$field])) {
                $dataForUpdate[self::APOLLO_TO_CUSTOM_FIELD_MAPPING[$field]] = $value;
            }
        }

        //@todo  handle addresses

        $dataUpdated = false;
        foreach ($dataForUpdate as $field => $value) {
            if (!isset($allAvailableCustomFields[$field])) {
                continue;
            }
            $typeColumn = CustomField::$attributeTypeFields[$allAvailableCustomFields[$field]['type']];

            if ($allAvailableCustomFields[$field]['type'] === CustomField::FIELD_TYPE_INTERNAL) {
                if (empty($lead->{$field})) {
                    $model->{$field} = $value;
                }

                continue;
            }

            //  Apollo data should NEVER overwrite data in CRM. Only Empty/blank/null fields should be filled in.

            if (empty($customFieldValues[$field]['value'])) {
                if (isset($customFieldValues[$field])) {
                    CustomFieldValues::query()->where('id', $customFieldValues[$field]['id'])
                        ->update([$typeColumn => $value]);
                } else {
                    CustomFieldValues::query()->create([
                        'field_id' => $allAvailableCustomFields[$field]['id'],
                        'entity_id' => $model->getKey(),
                        'entity' => $model::class,
                        $typeColumn => $value,

                    ]);
                }
                $dataUpdated = true;
            }
        }

        $this->syncAddress($address, $dataUpdated, $customFieldValues, $allAvailableCustomFields, $model);


        if ($dataUpdated) {
            $this->repository->update($model, ['updated_by' => $user->getKey()]);
        }

        $model->apollo_synced_at = now();
        $model->save();
    }


    private function syncAddress(
        array $address,
        bool &$dataUpdated,
        array $customFieldValues,
        array $allAvailableCustomFields,
        BaseModelWithCustomFields $model,
    ): void {
        if (!empty(array_filter($address))) {
            $field = 'addresses';
            $typeColumn = 'json_value';
            if (isset($customFieldValues['addresses'])) {
                if (is_array($customFieldValues[$field]['value'])) {
                    $existedAddresses = $customFieldValues[$field]['value'];

                    $addressExist = false;

                    foreach ($existedAddresses as $savedAddress) {
                        if (
                            ($savedAddress->city ?? '') == $address['city'] &&
                            ($savedAddress->state ?? '') == $address['state'] &&
                            ($savedAddress->country ?? '') == $address['country'] &&
                            ($savedAddress->postal_code ?? '') == $address['postal_code'] &&
                            ($savedAddress->street_address ?? '') == $address['street_address']
                        ) {
                            $addressExist = true;
                            break;
                        }
                    }

                    if (!$addressExist) {
                        $existedAddresses[] = $address;
                        CustomFieldValues::query()->where('id', $customFieldValues[$field]['id'])
                            ->update([$typeColumn => $existedAddresses]);
                        $dataUpdated = true;
                    }
                } else {
                    CustomFieldValues::query()->where('id', $customFieldValues[$field]['id'])
                        ->update([$typeColumn => [$address]]);
                    $dataUpdated = true;
                }
            } else {
                CustomFieldValues::query()->create([
                    'field_id' => $allAvailableCustomFields[$field]['id'],
                    'entity_id' => $model->getKey(),
                    'entity' => $model::class,
                    $typeColumn => [$address],

                ]);

                $dataUpdated = true;
            }
        }
    }
}
