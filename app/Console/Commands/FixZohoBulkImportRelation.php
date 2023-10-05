<?php

namespace App\Console\Commands;

use App\Helpers\CommonHelper;
use App\Models\Account;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\CustomField;
use App\Models\CustomFieldValues;
use App\Models\Estimate;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\User;
use App\Models\ZohoEntityExport;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class FixZohoBulkImportRelation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-zoho-bulk-import-relation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private const OUR_ENTITY_TO_ZOHO_RELATION = [
        'App\Models\Lead' => 'Leads',
        'App\Models\Account' => 'Accounts',
        'App\Models\Contact' => 'Contacts',
        'App\Models\Opportunity' => 'Deals',
        'App\Models\Activity' => 'Tasks',
        'App\Models\Estimate' => 'Quotes',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Update missed Opportunity Account name
        echo 'Start =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
        Log::debug('debug start fix relation');
        $this->updateMissedCustomFieldRelation(new Opportunity(), 'account-name', 'Account_Name', new Account());
        echo 'first step =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
        /*        $customField = CustomField::query()->where('entity_type', Opportunity::class)
                      ->where('code', 'account-name')->first();

                  Opportunity::query()->whereNotNull('zoho_entity_id')->whereDoesntHave(
                      'customFieldValues',
                      function ($query) use ($customField) {
                          $query->where('field_id', $customField->getKey());
                      },
                  )->chunk(50, function ($opportunityCollection) use ($customField) {
                      foreach ($opportunityCollection as $opportunity) {
                          $zohoEntity = ZohoEntityExport::query()->where('entity_type', 'Deals')
                              ->where('entity_id', $opportunity->zoho_entity_id)->first();

                          if (empty($zohoEntity)) {
                              continue;
                          }

                          $zohoEntityData = $zohoEntity->data;
                          $accountId = $zohoEntityData['Account_Name'] ?? '';

                          if (empty($accountId)) {
                              continue;
                          }

                          $ourAccount = Account::query()->where('zoho_entity_id', $accountId)->first();

                          if (empty($ourAccount)) {
                              continue;
                          }

                          CustomFieldValues::query()->create([
                              'field_id' => $customField->getKey(),
                              'entity_id' => $opportunity->getKey(),
                              'entity' => Opportunity::class,
                              'integer_value' => $ourAccount->getKey(),
                          ]);
                      }
                  });
   */
        $itemsToWork = Estimate::query()->where(function ($query) {
            $query->whereNull('opportunity_id')->orWhereNull('account_id')
                ->orWhereNull('contact_id')->whereNotNull('zoho_entity_id');
        })->where(function (Builder $query) {
            $query->whereDoesntHave('zohoRelationSyncStatus', function ($query) {
                $query->where('synced_at', '>', now()->subMonth());
            });
        })->count();
        echo 'We have total ' . $itemsToWork . ' estimate to process' . PHP_EOL;


        //Update missed Estimate(Quote) values
        Estimate::query()->where(function ($query) {
            $query->whereNull('opportunity_id')->orWhereNull('account_id')
                ->orWhereNull('contact_id')->whereNotNull('zoho_entity_id');
        })->where(function (Builder $query) {
            $query->whereDoesntHave('zohoRelationSyncStatus', function ($query) {
                $query->where('synced_at', '>', now()->subMonth());
            });
        })
            ->chunkById(50, function ($estimateCollection) {
                echo 'step =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
                foreach ($estimateCollection as $estimate) {
                    $zohoEntity = ZohoEntityExport::query()->where(
                        'entity_type',
                        self::OUR_ENTITY_TO_ZOHO_RELATION[Estimate::class],
                    )
                        ->where('entity_id', $estimate->zoho_entity_id)->first();

                    if (empty($zohoEntity)) {
                        $estimate->zohoRelationSyncStatus()->create([
                            'synced_at' => now(),
                        ]);
                        continue;
                    }

                    $zohoEntityData = $zohoEntity->data;

                    if (empty($estimate->opportunity_id)) {
                        $opportunityId = $zohoEntityData['Deal_Name'] ?? '';
                        if (!empty($opportunityId)) {
                            $ourOpportunity = Opportunity::query()->where('zoho_entity_id', $opportunityId)->first();
                            if (!empty($ourOpportunity)) {
                                $estimate->opportunity_id = $ourOpportunity->getKey();
                            }
                        }
                    }

                    if (empty($estimate->account_id)) {
                        $accountId = $zohoEntityData['Account_Name'] ?? '';
                        if (!empty($accountId)) {
                            $ourAccount = Account::query()->where('zoho_entity_id', $accountId)->first();
                            if (!empty($ourAccount)) {
                                $estimate->account_id = $ourAccount->getKey();
                            }
                        } else {
                            $defaultAccount = CommonHelper::getOrCreateDefaultAccount();
                            if (!empty($defaultAccount)) {
                                $estimate->account_id = $defaultAccount->getKey();
                            }
                        }
                    }

                    if (empty($estimate->contact_id)) {
                        $contactId = $zohoEntityData['Contact_Name'] ?? '';
                        if (!empty($contactId)) {
                            $ourContact = Contact::query()->where('zoho_entity_id', $contactId)->first();
                            if (!empty($ourContact)) {
                                $estimate->contact_id = $ourContact->getKey();
                            }
                        } else {
                            $defaultContact = CommonHelper::getOrCreateDefaultContactForAccount($estimate->account_id);
                            if (!empty($defaultContact)) {
                                $estimate->contact_id = $defaultContact->getKey();
                            }
                        }
                    }

                    $estimate->save();
                    $estimate->zohoRelationSyncStatus()->create([
                        'synced_at' => now(),
                    ]);
                }
            });
        echo 'After estimate =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;

        Activity::query()->where('related_to', 1)->whereNotNull('zoho_entity_id')
            ->chunkById(50, function ($collection) {
                echo 'step =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
                foreach ($collection as $item) {
                    $zohoEntity = ZohoEntityExport::query()->where(
                        'entity_type',
                        self::OUR_ENTITY_TO_ZOHO_RELATION[Activity::class],
                    )
                        ->where('entity_id', $item->zoho_entity_id)->first();

                    if (empty($zohoEntity)) {
                        $item->zohoRelationSyncStatus()->create([
                            'synced_at' => now(),
                        ]);
                        continue;
                    }

                    $zohoEntityData = $zohoEntity->data;


                    $relatedEntity = $zohoEntityData['Owner'] ?? '';
                    $ourRecord = User::query()->where('zoho_entity_id', $relatedEntity)->first();
                    if (!empty($ourRecord)) {
                        $item->related_to = $ourRecord->getKey();
                    }

                    $item->save();

                    $item->zohoRelationSyncStatus()->create([
                        'synced_at' => now(),
                    ]);
                }
            });
        echo 'After activity =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;

        Activity::query()->whereNull('related_to_id')->whereNotNull('zoho_entity_id')->chunkById(
            50,
            function ($collection) {
                echo 'step =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
                foreach ($collection as $item) {
                    $zohoEntity = ZohoEntityExport::query()->where(
                        'entity_type',
                        self::OUR_ENTITY_TO_ZOHO_RELATION[$item->related_to_entity],
                    )
                        ->where('entity_id', $item->zoho_entity_id)->first();

                    if (empty($zohoEntity)) {
                        continue;
                    }

                    $zohoEntityData = $zohoEntity->data;

                    $relatedEntity = $zohoEntityData['What_Id'] ?? '';
                    $ourRecord = app($item->related_to_entity)::query()->where('zoho_entity_id', $relatedEntity)->first(
                    );
                    if (!empty($ourRecord)) {
                        $item->related_to_id = $ourRecord->getKey();
                    }

                    $item->save();
                    $item->zohoRelationSyncStatus()->create([
                        'synced_at' => now(),
                    ]);
                }
            },
        );
        echo 'after activity2 =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;

        //update missed lead owner
        $this->updateMissedCustomFieldRelation(new Lead(), 'lead-owner', 'Owner', new User());

        echo 'step3=' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
//update missed account owner
        $this->updateMissedCustomFieldRelation(new Account(), 'account-owner', 'Owner', new User());
        echo 'step4 =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
        //update missed contact owner
        $this->updateMissedCustomFieldRelation(new Contact(), 'contact-owner', 'Owner', new User());
        echo 'step5 =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
        //update missed contact  training completed by
        $this->updateMissedCustomFieldRelation(new Contact(), 'training-by', 'Training_Completed_By', new User());
        echo 'step6 =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
        //update Opportunity
        $this->updateMissedCustomFieldRelation(new Opportunity(), 'opportunity-owner', 'Owner', new User());
        echo 'step7 =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
        $this->updateMissedCustomFieldRelation(new Opportunity(), 'account-name', 'Account_Name', new Account());
        echo 'step8 =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
        $this->updateMissedCustomFieldRelation(new Estimate(), 'estimate-owner', 'Owner', new User());
        echo 'step9 =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
        Log::debug('debug end fix relation');
    }


    private function updateMissedCustomFieldRelation(
        Model $entityType,
        string $customFieldCode,
        string $zohoRelatedEntityName,
        Model $ourRelatedTable,
    ): void {
        $customField = CustomField::query()->where('entity_type', $entityType::class)
            ->where('code', $customFieldCode)->first();

        if (!$customField) {
            return;
        }
        echo 'step =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
        $totalRecords = $entityType::query()->whereNotNull('zoho_entity_id')->whereDoesntHave(
            'customFieldValues',
            function ($query) use ($customField) {
                $query->where('field_id', $customField->getKey());
            },
        )->count();
        echo 'Total records to work = ' . $totalRecords . PHP_EOL;

        $entityType::query()->whereNotNull('zoho_entity_id')->whereDoesntHave(
            'customFieldValues',
            function ($query) use ($customField) {
                $query->where('field_id', $customField->getKey());
            },
        )->chunkById(
            50,
            function ($collection) use ($customField, $entityType, $zohoRelatedEntityName, $ourRelatedTable) {
                echo 'step chunk =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
                foreach ($collection as $item) {
                    $zohoEntity = ZohoEntityExport::query()->where(
                        'entity_type',
                        self::OUR_ENTITY_TO_ZOHO_RELATION[$entityType::class],
                    )->where('entity_id', $item->zoho_entity_id)->first();

                    if (empty($zohoEntity)) {
                        continue;
                    }

                    $zohoEntityData = $zohoEntity->data;
                    $relatedEntity = $zohoEntityData[$zohoRelatedEntityName] ?? '';

                    if (empty($relatedEntity)) {
                        // If missed data then no need to rerun this.
                        CustomFieldValues::query()->create([
                            'field_id' => $customField->getKey(),
                            'entity_id' => $item->getKey(),
                            'entity' => $entityType::class,
                            'integer_value' => null,
                        ]);

                        continue;
                    }

                    $ourRecord = $ourRelatedTable::query()->where('zoho_entity_id', $relatedEntity)->first();

                    if (empty($ourRecord)) {
                        continue;
                    }

                    CustomFieldValues::query()->create([
                        'field_id' => $customField->getKey(),
                        'entity_id' => $item->getKey(),
                        'entity' => $entityType::class,
                        'integer_value' => $ourRecord->getKey(),
                    ]);
                }
            },
        );
    }
}
