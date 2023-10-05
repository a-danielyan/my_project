<?php

namespace App\Helpers\ZohoImport;

use App\Exceptions\CustomErrorException;
use App\Exceptions\InvalidMappingErrorException;
use App\Helpers\CommonHelper;
use App\Http\Repositories\BaseRepository;
use App\Models\Account;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\CustomField;
use App\Models\CustomFieldOption;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\User;
use com\zoho\crm\api\record\Record;
use com\zoho\crm\api\users\MinifiedUser;
use com\zoho\crm\api\util\Choice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BaseZohoMapper implements ZohoMapperInterface
{
    public function getMappingValues(): array
    {
        return [];
    }

    public function getEntityClassName(): string
    {
        return '';
    }

    public function getRepository(): BaseRepository
    {
        return resolve(BaseRepository::class);
    }

    public function getInternalFields(array $zohoData, bool $isUpdate = false): array
    {
        $salutation = null;
        if (!empty($zohoData['Salutation'])) {
            $salutation = $zohoData['Salutation'];

            if (!str_ends_with($salutation, '.')) {
                $salutation = $salutation . '.';
            }
            if (!in_array($salutation, ['Mr.', 'Ms.', 'Dr.'])) {
                $salutation = null;
            }
        }

        $cronUser = CommonHelper::getCronUser();
        $internalFields = [
            'zoho_entity_id' => $zohoData['Id'],
            'created_by' => $cronUser->getKey(),
            'salutation' => $salutation,
        ];

        if ($isUpdate) {
            $internalFields = array_filter($internalFields);
            $internalFields['updated_by'] = $cronUser->getKey();
        }

        return $internalFields;
    }

    /**
     * @param CustomField $ourCustomField
     * @param array $zohoData
     * @return string
     * @throws InvalidMappingErrorException
     */
    public function mapZohoFieldsToOurCustomFields(
        CustomField $ourCustomField,
        array $zohoData,
    ): string {
        $mappedValues = $this->getMappingValues();
        if (!isset($mappedValues[$ourCustomField->code]) || empty($zohoData[$mappedValues[$ourCustomField->code]])) {
            throw new InvalidMappingErrorException();
        }

        $zohoValue = $this->getZohoMapperValueAsString($zohoData[$mappedValues[$ourCustomField->code]]);
        switch ($ourCustomField->type) {
            case CustomField::FIELD_TYPE_LOOKUP:
                return $this->searchLookUpValues($ourCustomField, $zohoValue);

            case CustomField::FIELD_TYPE_TEXTAREA:
            case CustomField::FIELD_TYPE_TEXT:
            case CustomField::FIELD_TYPE_DATE:
            case CustomField::FIELD_TYPE_NUMBER:
            case CustomField::FIELD_TYPE_PHONE:
            case CustomField::FIELD_TYPE_EMAIL:
            case CustomField::FIELD_TYPE_PRICE:
                return $zohoValue;

            case CustomField::FIELD_TYPE_SELECT:
            case CustomField::FIELD_TYPE_MULTISELECT:
                /** @var CustomFieldOption $optionsValue */
                $optionsValue = $ourCustomField->options()->where(
                    'name',
                    $zohoValue,
                )->first();
                if ($optionsValue) {
                    return $optionsValue->id;
                } else {
                    $customFieldOption = CustomFieldOption::query()->create(
                        [
                            'name' => $zohoValue,
                            'sort_order' => 1,
                            'custom_field_id' => $ourCustomField->getKey(),
                        ],
                    );

                    return $customFieldOption->getKey();
                }
        }

        throw new InvalidMappingErrorException();
    }

    /**
     * @param CustomField $customField
     * @param string $value
     * @return int|null
     * @throws InvalidMappingErrorException
     */
    protected function searchLookUpValues(CustomField $customField, string $value): ?int
    {
        switch ($customField->lookup_type) {
            case CustomField::LOOKUP_TYPE_LEAD_SOURCE:
            case CustomField::LOOKUP_TYPE_LEAD_STATUS:
            case CustomField::LOOKUP_TYPE_LEAD_TYPE:
            case CustomField::LOOKUP_TYPE_SOLUTION:
            case CustomField::LOOKUP_TYPE_INDUSTRY:
                return Cache::remember(
                    $customField->lookup_type . '#' . $value . 'search',
                    600,
                    function () use ($customField, $value) {
                        $lookupValue = DB::table($customField->lookup_type)->where('name', $value)->first();
                        if (!$lookupValue) {
                            $cronUser = CommonHelper::getCronUser();
                            DB::table($customField->lookup_type)->insert([
                                'name' => $value,
                                'created_by' => $cronUser->getKey(),
                            ]);
                            $lookupValue = DB::table($customField->lookup_type)->where('name', $value)->first();
                        }

                        return $lookupValue->id;
                    },
                );


            default:
                throw new InvalidMappingErrorException();
        }
    }

    public function beforeMap(array $zohoData): array
    {
        return $zohoData;
    }

    /**
     * @param mixed $zohoValue
     * @return string
     */
    protected function getZohoMapperValueAsString(mixed $zohoValue): string
    {
        if ($zohoValue instanceof Record) {
            $value = $zohoValue->getId();
        } elseif ($zohoValue instanceof Choice) {
            $value = $zohoValue->getValue();
        } elseif ($zohoValue instanceof MinifiedUser) {
            $value = $zohoValue->getId();
        } else {
            $value = $zohoValue;
        }

        return (string)$value;
    }

    public function afterInserted(Model $model, array $zohoData): void
    {
    }

    /**
     * @param int $zohoEntityId
     * @return int
     * @throws CustomErrorException
     */
    protected function getRelatedUserId(int $zohoEntityId): int
    {
        return Cache::remember('ZohoUser#' . $zohoEntityId, 86400, function () use ($zohoEntityId) {
            $user = User::query()->where('zoho_entity_id', $zohoEntityId)->first();
            if ($user) {
                return $user->getKey();
            }
            throw new CustomErrorException('Related user with id ' . $zohoEntityId . ' not found');
        });
    }

    /**
     * @param string $relatedModule
     * @param string $relatedZohoId
     * @return int
     * @throws CustomErrorException
     */
    protected function getRelatedId(string $relatedModule, string $relatedZohoId): int
    {
        $query = match ($relatedModule) {
            'Leads' => Lead::query(),
            'Accounts' => Account::query(),
            'Contacts' => Contact::query(),
            'Deals' => Opportunity::query(),
            'Tasks' => Activity::query(),
            'Quotes' => Estimate::query(),
            'Sales_Orders' => Invoice::query(),
            default => throw new CustomErrorException('Unknown module ' . $relatedModule),
        };

        if (empty($relatedZohoId)) {
            throw new CustomErrorException($relatedModule . '  with Id ' . $relatedZohoId . ' not found');
        }

        return Cache::remember(
            'Zoho' . $relatedModule . '#' . $relatedZohoId,
            86400,
            function () use ($query, $relatedModule, $relatedZohoId) {
                if ($relatedModule === 'Sales_Orders') {
                    $ourRecord = $query->where('zoho_entity_id_sales_order', $relatedZohoId)->first();
                } else {
                    $ourRecord = $query->where('zoho_entity_id', $relatedZohoId)->first();
                }

                if ($ourRecord) {
                    return $ourRecord->getKey();
                }
                throw new CustomErrorException($relatedModule . '  with Id ' . $relatedZohoId . ' not found');
            },
        );
    }
}
