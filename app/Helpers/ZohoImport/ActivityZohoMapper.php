<?php

namespace App\Helpers\ZohoImport;

use App\Exceptions\CustomErrorException;
use App\Helpers\CommonHelper;
use App\Http\Repositories\ActivityRepository;
use App\Http\Repositories\BaseRepository;
use App\Models\Activity;
use App\Models\Invoice;

class ActivityZohoMapper extends BaseZohoMapper
{
    private const ZOHO_ENTITY_TO_OUR_RECORDS_RELATION = [
        'Leads' => 'App\Models\Lead',
        'Accounts' => 'App\Models\Account',
        'Contacts' => 'App\Models\Contact',
        'Deals' => 'App\Models\Opportunity',
        'Tasks' => 'App\Models\Activity',
        'Quotes' => 'App\Models\Estimate',
        'Sales_Orders' => Invoice::class,
    ];

    public function getMappingValues(): array
    {
        return [
            'related_to' => 'Owner',
            // 'activity_type',
            'activity_status' => 'Status',
            'priority' => 'Priority',
            'due_date' => 'Due_Date',
            'subject' => 'Subject',
            // 'related_to_entity',
            //'related_to_id',
            'description' => 'Description',
            // 'reminder_at',
            // 'reminder_type',    //$se_module
        ];
    }

    public function getEntityClassName(): string
    {
        return Activity::class;
    }

    public function getRepository(): BaseRepository
    {
        return resolve(ActivityRepository::class);
    }

    /**
     * @param array $zohoData
     * @param bool $isUpdate
     * @return array
     * @throws CustomErrorException
     */
    public function getInternalFields(array $zohoData, bool $isUpdate = false): array
    {
        $relatedItemId = 0;

        if (!empty($zohoData['What_Id'])) {
            $relatedItemId = $this->getZohoMapperValueAsString($zohoData['What_Id']);
        } elseif (!empty($zohoData['Who_Id'])) {
            $relatedItemId = $this->getZohoMapperValueAsString($zohoData['Who_Id']);
        }
        $cronUser = CommonHelper::getCronUser();

        $internalFields = [
            'zoho_entity_id' => $zohoData['Id'],
            'related_to' => $this->getRelatedUserId($this->getZohoMapperValueAsString($zohoData['Owner'])),
            'activity_type' => 'Task',
            'activity_status' => $this->getZohoMapperValueAsString($zohoData['Status']),
            'priority' => $this->getZohoMapperValueAsString($zohoData['Priority']),
            'due_date' => !empty($zohoData['Due_Date']) ? $this->getZohoMapperValueAsString(
                $zohoData['Due_Date'],
            ) : null,
            'subject' => $zohoData['Subject'],
            'related_to_entity' => self::ZOHO_ENTITY_TO_OUR_RECORDS_RELATION[$zohoData['$se_module']] ?? null,
            'related_to_id' => $this->getRelatedId($zohoData['$se_module'], (int)$relatedItemId),
            'description' => $zohoData['Description'],
            'created_by' => $cronUser->getKey(),
        ];


        if ($isUpdate) {
            $internalFields = array_filter($internalFields);
            $internalFields['updated_by'] = $cronUser->getKey();
        }

        return $internalFields;
    }
}
