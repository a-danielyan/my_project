<?php

namespace App\Http\Services;

use App\Exceptions\ConvertLeadErrorException;
use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelCreateErrorException;
use App\Helpers\CustomFieldValuesHelper;
use App\Http\Repositories\OpportunityRepository;
use App\Http\Resource\LeadResource;
use App\Models\Account;
use App\Models\AccountAttachment;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\ContactAuthority;
use App\Models\CustomField;
use App\Models\CustomFieldValues;
use App\Models\EmailToEntityAssociation;
use App\Models\EntityLog;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\Opportunity;
use App\Models\Stage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use PDOException;
use Throwable;

class LeadToAccountContactConvertService
{
    public const ACTION_ADD_TO_EXISTING = 'addToExisting';
    public const ACTION_CREATE_NEW = 'createNew';

    public function resource(): string
    {
        return LeadResource::class;
    }

    /**
     * @param Lead $lead
     * @param User|Authenticatable $user
     * @param array $data
     * @return array
     * @throws ConvertLeadErrorException
     * @throws ModelCreateErrorException
     */
    public function convertToContactAccount(Lead $lead, User|Authenticatable $user, array $data): array
    {
        DB::beginTransaction();
        try {
            $leadCustomFields = $this->prepareLeadCustomFields($lead);

            $accountNameCustomField = CustomField::query()->where('entity_type', Account::class)
                ->where('code', 'account-name')->first();

            if (!$accountNameCustomField) {
                throw new CustomErrorException(
                    'Company account name field missed. Please contact system administrator',
                    422,
                );
            }

            /** @var CustomFieldValues $existedAccountCustomFieldValue */
            $existedAccountCustomFieldValue = CustomFieldValues::query()->where(
                'text_value',
                'LIKE',
                substr($leadCustomFields['company']->text_value, 0, 4) . '%',
            )
                ->where('field_id', $accountNameCustomField->getKey())->first();


            $contactEmailCustomField = CustomField::query()->where('entity_type', Contact::class)
                ->where('code', 'email')->first();
            if (!$contactEmailCustomField) {
                throw new CustomErrorException(
                    'Contact email field missed. Please contact system administrator',
                    422,
                );
            }

            /** @var CustomFieldValues $existedContactCustomFieldValues */
            $existedContactCustomFieldValues = CustomFieldValues::query()->where(
                'text_value',
                $leadCustomFields['email']->text_value,
            )
                ->where('field_id', $contactEmailCustomField->getKey())->first();

            $existedAccount = null;
            $existedContact = null;
            $existedOpportunities = null;
            if ($existedAccountCustomFieldValue || $existedContactCustomFieldValues) {
                if (
                    ($existedContactCustomFieldValues && (!isset($data['actionContact']))) ||
                    ($existedAccountCustomFieldValue && !isset($data['actionAccount']))
                ) {
                    if ($existedAccountCustomFieldValue) {
                        /** @var Account $existedAccount */
                        $existedAccount = Account::query()->with(['customFields'])->find(
                            $existedAccountCustomFieldValue->entity_id,
                        );
                        $existedOpportunities = $existedAccount->opportunities()
                            ->whereHas(
                                'stage',
                                function ($query) {
                                    $query->where('name', '!=', Stage::CLOSED_WON_STAGE)
                                        ->where('name', '!=', Stage::CLOSED_LOST_STAGE);
                                },
                            )->get();
                    }

                    if ($existedContactCustomFieldValues) {
                        $existedContact = Contact::query()->with(['customFields'])->find(
                            $existedContactCustomFieldValues->entity_id,
                        );
                    }

                    throw new ConvertLeadErrorException($existedAccount, $existedContact, $lead, $existedOpportunities);
                }
            }

            if ($existedAccountCustomFieldValue) {
                $account = $this->handleAccountAction(
                    $data['actionAccount'],
                    $existedAccountCustomFieldValue,
                    $user,
                    $leadCustomFields,
                    $lead,
                    $data['accountCustomFields'] ?? [],
                );
            } else {
                $account = $this->createAccount($lead, $user, $leadCustomFields);
            }

            if ($existedContactCustomFieldValues) {
                $contact = $this->handleContactAction(
                    $data['actionContact'],
                    $existedContactCustomFieldValues,
                    $user,
                    $leadCustomFields,
                    $lead,
                    $account,
                    $data['contactCustomFields'] ?? [],
                );
            } else {
                $contact = $this->createContact($lead, $account, $user, $leadCustomFields);
            }

            $opportunity = $this->createOpportunity(
                $data,
                $user,
                $account,
                $contact,
            );

            $this->updateLeadStatusAsConverted($lead);
            $lead->delete();

            $this->copyAttachmentToAccount($lead, $account);
            $this->copyTaskToContact($lead, $contact);
            $this->copyEmailsToContact($lead, $contact);

            $leadCustomFieldsById = $lead->customFields->mapWithKeys(function ($item) {
                if ($item->customField) {
                    return [$item->customField->id => $item->customField->code];
                }

                return [];
            })->toArray();

            $contactCustomFieldsById = CustomField::query()->where('entity_type', Contact::class)->get()->mapWithKeys(
                function ($item) {
                    return [$item->id => $item->code];
                },
            )->toArray();

            $accountCustomFieldsById = CustomField::query()->where('entity_type', Account::class)->get()->mapWithKeys(
                function ($item) {
                    return [$item->id => $item->code];
                },
            )->toArray();

            $this->copyEntityLogToContact($lead, $contact, $leadCustomFieldsById, $contactCustomFieldsById);
            $this->copyEntityLogToAccount($lead, $account, $leadCustomFieldsById, $accountCustomFieldsById);
            $this->copyAvatarToContact($lead, $contact);

            DB::commit();

            return [
                'accountId' => $account->getKey(),
                'contactId' => $contact->getKey(),
                'opportunityId' => $opportunity?->getKey(),
            ];
        } catch (ConvertLeadErrorException $e) {
            if (DB::transactionLevel()) {
                DB::rollBack();
            }
            throw $e;
        } catch (Throwable $e) {
            if (DB::transactionLevel()) {
                DB::rollBack();
            }
            throw new ModelCreateErrorException($e->getMessage());
        }
    }

    /**
     * @param Lead $lead
     * @return Collection
     * @throws CustomErrorException
     */
    private function prepareLeadCustomFields(Lead $lead): Collection
    {
        $leadCustomFields = $lead->customFields->mapWithKeys(function ($item) {
            if ($item->customField) {
                return [$item->customField->code => $item];
            }

            return [];
        });
        if (!isset($leadCustomFields['company'])) {
            throw new CustomErrorException('Lead company name missed', 422);
        }

        $leadCompanyName = $leadCustomFields['company'];
        $leadCustomFields['account-name'] = $leadCompanyName;
        $leadCustomFields['description'] = $leadCustomFields['lead-description'] ?? null;
        $leadCustomFields['contact-owner'] = $leadCustomFields['lead-owner'] ?? null;
        $leadCustomFields['lead-created-on'] = new CustomFieldValues(['datetime_value' => $lead->created_at]);
        if (!isset($leadCustomFields['email'])) {
            throw new CustomErrorException('Lead email missed', 422);
        }

        return $leadCustomFields;
    }


    private function copyCustomFieldValue(
        CustomFieldValues $originalCustomField,
        Model $newEntity,
        CustomField $customField,
    ): void {
        $values = $originalCustomField->only([
            'text_value',
            'boolean_value',
            'integer_value',
            'float_value',
            'datetime_value',
            'date_value',
            'json_value',
        ]);
        if (!empty($values['json_value'])) {
            $values['text_value'] = null;
        }
        if (is_object($values['text_value'])) {
            $values['text_value'] = null;
        }
        if ($values['datetime_value'] instanceof Carbon) {
            $values['datetime_value'] = $values['datetime_value']->format('Y-m-d H:i:s');
        }


        $customFieldValueData = array_merge([
            'field_id' => $customField->getKey(),
            'entity_id' => $newEntity->getKey(),
            'entity' => $newEntity::class,
        ], $values);

        CustomFieldValues::query()->create($customFieldValueData);
    }

    private function updateCustomFieldValue(
        string|int|array $fieldValue,
        Model $newEntity,
        CustomField $customField,
    ): void {
        $typeColumn = CustomField::$attributeTypeFields[$customField->type];

        CustomFieldValues::query()->where([
            'field_id' => $customField->getKey(),
            'entity_id' => $newEntity->getKey(),
        ])->update([$typeColumn => $fieldValue]);
    }

    private function createAccount(Lead $lead, User|Authenticatable $user, Collection $leadCustomFields): Account
    {
        /** @var Account $account */
        $account = Account::query()->create([
            'created_by' => $user->getKey(),
        ]);

        $accountCustomFields = CustomField::query()->where('entity_type', Account::class)->get();

        foreach ($accountCustomFields as $customField) {
            if (isset($leadCustomFields[$customField->code])) {
                $this->copyCustomFieldValue($leadCustomFields[$customField->code], $account, $customField);
            }
        }

        return $account;
    }

    private function updateExistedAccount(
        Account $account,
        User|Authenticatable $user,
        array $accountCustomFields,
    ): void {
        $accountCustomFieldsExisted = CustomField::query()->where('entity_type', Account::class)->get();

        foreach ($accountCustomFieldsExisted as $customField) {
            if (isset($accountCustomFields[$customField->code])) {
                $this->updateCustomFieldValue(
                    $accountCustomFields[$customField->code],
                    $account,
                    $customField,
                );
            }
        }
        $account->updated_by = $user->getKey();
        $account->save();
    }


    private function createContact(
        Lead $lead,
        Account $account,
        User|Authenticatable $user,
        Collection $leadCustomFields,
    ): Contact {
        /** @var Contact $contact */
        $contact = Contact::query()->create([
            'salutation' => $lead->salutation,
            'account_id' => $account->getKey(),
            'created_by' => $user->getKey(),
        ]);

        $contactCustomFields = CustomField::query()->where('entity_type', Contact::class)->get();

        foreach ($contactCustomFields as $customField) {
            if (isset($leadCustomFields[$customField->code])) {
                $this->copyCustomFieldValue($leadCustomFields[$customField->code], $contact, $customField);
            }
        }


        $this->setContactAuthorityDefaultValue($contact, $user);
        $this->setAccountAddressLeadAddressMissed($leadCustomFields, $account, $contact, $user);

        return $contact;
    }

    /**
     * @param array $data
     * @param User $user
     * @param Account $account
     * @param Contact $contact
     * @return Opportunity|null
     * @throws CustomErrorException
     */
    private function createOpportunity(
        array $data,
        User $user,
        Account $account,
        Contact $contact,
    ): ?Opportunity {
        if ($data['doNotCreateOpportunity'] === true) {
            return null;
        }
        if (isset($data['actionAccount']) && $data['actionAccount'] === self::ACTION_CREATE_NEW) {
            $data['actionOpportunity'] = self::ACTION_CREATE_NEW;
        }
        /** @var OpportunityRepository $opportunityRepository */
        $opportunityRepository = resolve(OpportunityRepository::class);

        $accountCustomFields = CustomFieldValuesHelper::getCustomFieldValues(
            $account,
            ['account-name', 'solution-interest'],
        );


        switch ($data['actionOpportunity'] ?? '') {
            case self::ACTION_ADD_TO_EXISTING:
                /** @var Opportunity $opportunity */
                $opportunity = Opportunity::query()->find($data['selectedOpportunityId']);

                if (!$opportunity) {
                    throw new CustomErrorException('Existed opportunity not found');
                }
                $opportunity->account_id = $account->getKey();
                $opportunity->save();
                break;

            default:
                $opportunityCustomFields = $data['opportunityCustomFields'] ?? [];

                if (!empty($opportunityCustomFields['opportunityName'])) {
                    $opportunityName = $opportunityCustomFields['opportunityName'];
                } else {
                    $solutionInterest = $accountCustomFields['solution-interest']?->id ?? 'none';

                    $opportunityName = $accountCustomFields['account-name'] . '_' .
                        $solutionInterest . '_' .
                        date('FY');
                }

                if (!empty($opportunityCustomFields['expectedClosingDate'])) {
                    $expectingClosingDate = $opportunityCustomFields['expectedClosingDate'];
                } else {
                    $carbonNow = now();
                    $carbonEndOfCurrentQuarter = $carbonNow->copy()->lastOfQuarter();

                    $daysLeftInCurrentQuarter = $carbonNow->diffInDays($carbonEndOfCurrentQuarter);

                    if ($daysLeftInCurrentQuarter > 15) {
                        $expectingClosingDate = $carbonEndOfCurrentQuarter;
                    } else {
                        $expectingClosingDate = $carbonEndOfCurrentQuarter->addDays(7)->endOfQuarter();
                    }
                    $expectingClosingDate = $expectingClosingDate->format('Y-m-d');
                }

                if (!empty($opportunityCustomFields['stageId'])) {
                    $stageId = $opportunityCustomFields['stageId'];
                } else {
                    $opportunityStage = Stage::query()->orderBy('sort_order')->first();
                    $stageId = $opportunityStage->getKey();
                }
                if (!empty($opportunityCustomFields['projectType'])) {
                    $projectType = $opportunityCustomFields['projectType'];
                } else {
                    $projectType = Opportunity::EXISTED_BUSINESS;
                }

                if (!empty($opportunityCustomFields['solutionInterest'])) {
                    CustomFieldValuesHelper::insertCustomFieldValue(
                        'solution-interest',
                        $opportunityCustomFields['solutionInterest'],
                        $contact->getKey(),
                        Contact::class,
                        $user,
                    );
                }

                if (!empty($opportunityCustomFields['contactAuthority'])) {
                    CustomFieldValuesHelper::insertCustomFieldValue(
                        'authority',
                        $opportunityCustomFields['contactAuthority'],
                        $contact->getKey(),
                        Contact::class,
                        $user,
                    );
                }

                $opportunityData = [
                    'created_by' => $user->getKey(),
                    'opportunity_name' => $opportunityName,
                    'project_type' => $projectType,
                    'expecting_closing_date' => $expectingClosingDate,
                    'stage_id' => $stageId,
                    'account_id' => $account->getKey(),
                ];
                /** @var Opportunity $opportunity */
                $opportunity = $opportunityRepository->create($opportunityData);

                break;
        }

        return $opportunity;
    }

    private function updateExistedContact(
        Contact $contact,
        int $accountId,
        User|Authenticatable $user,
        array $contactCustomFields,
    ): void {
        $contactCustomFieldsExisted = CustomField::query()->where('entity_type', Contact::class)->get();

        foreach ($contactCustomFieldsExisted as $customField) {
            if (isset($contactCustomFields[$customField->code])) {
                $this->updateCustomFieldValue(
                    $contactCustomFields[$customField->code],
                    $contact,
                    $customField,
                );
            }
        }
        $contact->account_id = $accountId;
        $contact->updated_by = $user->getKey();
        $contact->save();
    }

    private function copyAttachmentToAccount(Lead $lead, Account $account): void
    {
        foreach ($lead->attachments as $attachment) {
            AccountAttachment::query()->create([
                'account_id' => $account->getKey(),
                'attachment_file' => $attachment->attachment_file,
                'attachment_link' => $attachment->attachment_link,
                'created_by' => $attachment->created_by,
                'name' => $attachment->name,
            ]);
        }
    }

    private function copyTaskToContact(Lead $lead, Contact $contact): void
    {
        foreach ($lead->activity as $activity) {
            $activityData = $activity->only([
                'related_to',
                'started_at',
                'ended_at',
                'activity_type',
                'activity_status',
                'priority',
                'due_date',
                'subject',
                'description',
                'created_by',
                'status',
            ]);

            Activity::query()->create(
                array_merge($activityData, [
                    'related_to_entity' => Contact::class,
                    'related_to_id' => $contact->getKey(),
                ]),
            );
        }
    }


    private function copyEmailsToContact(Lead $lead, Contact $contact): void
    {
        foreach ($lead->emailAssociation as $emailAssociation) {
            try {
                EmailToEntityAssociation::query()->create([
                    'email_id' => $emailAssociation->email_id,
                    'entity_id' => $contact->getKey(),
                    'entity' => Contact::class,
                ]);
            } catch (PDOException) {
            }
        }
    }

    private function copyEntityLogToContact(
        Lead $lead,
        Contact $contact,
        array $leadCustomFieldsById,
        array $contactCustomFieldsById,
    ): void {
        foreach ($lead->timeline as $entityLog) {
            $entityLogData = $entityLog->only([
                'previous_value',
                'new_value',
                'updated_by',
                'update_id',
            ]);

            $entityLogData = array_merge(
                $entityLogData,
                ['entity' => Contact::class, 'entity_id' => $contact->getKey()],
            );
            if (!empty($entityLog->field_id)) {
                $fieldCode = $leadCustomFieldsById[$entityLog->field_id];
                $contactCode = array_search($fieldCode, $contactCustomFieldsById);
                $entityLogData['field_id'] = $contactCode;
            } else {
                $entityLogData['field_id'] = null;
            }

            EntityLog::query()->create($entityLogData);
        }
    }


    private function copyEntityLogToAccount(
        Lead $lead,
        Account $account,
        array $leadCustomFieldsById,
        array $accountCustomFieldsById,
    ): void {
        foreach ($lead->timeline as $entityLog) {
            $entityLogData = $entityLog->only([
                'previous_value',
                'new_value',
                'updated_by',
                'update_id',
            ]);

            $entityLogData = array_merge(
                $entityLogData,
                ['entity' => Account::class, 'entity_id' => $account->getKey()],
            );
            if (!empty($entityLog->field_id)) {
                $fieldCode = $leadCustomFieldsById[$entityLog->field_id];
                $contactCode = array_search($fieldCode, $accountCustomFieldsById);

                $entityLogData['field_id'] = $contactCode;
            } else {
                $entityLogData['field_id'] = null;
            }

            EntityLog::query()->create($entityLogData);
        }
    }

    private function copyAvatarToContact(Lead $lead, Contact $contact): void
    {
        if (!empty($lead->avatar)) {
            $contact->avatar = $lead->avatar;
            $contact->save();
        }
    }


    private function handleAccountAction(
        string $accountAction,
        CustomFieldValues $existedAccount,
        User|Authenticatable $user,
        Collection $leadCustomFields,
        Lead $lead,
        array $accountCustomFields,
    ): Account {
        switch ($accountAction) {
            case self::ACTION_ADD_TO_EXISTING:
                /** @var Account $account */
                $account = Account::query()->find($existedAccount->entity_id);
                $this->updateExistedAccount($account, $user, $accountCustomFields);
                break;
            default:
                // by default create new
                $leadCustomFields['account-name']->text_value =
                    $leadCustomFields['account-name']->text_value . ' copy';
                $account = $this->createAccount($lead, $user, $leadCustomFields);
                break;
        }

        return $account;
    }

    private function handleContactAction(
        string $actionContact,
        CustomFieldValues $existedContact,
        User|Authenticatable $user,
        Collection $leadCustomFields,
        Lead $lead,
        Account $account,
        array $contactCustomFields,
    ): Contact {
        switch ($actionContact) {
            case self::ACTION_ADD_TO_EXISTING:
                /** @var Contact $contact */
                $contact = Contact::query()->find($existedContact->entity_id);
                $this->updateExistedContact($contact, $account->getKey(), $user, $contactCustomFields);
                break;
            default:
                //by default create new
                $contact = $this->createContact($lead, $account, $user, $leadCustomFields);
                break;
        }

        return $contact;
    }

    private function updateLeadStatusAsConverted(Lead $lead): void
    {
        $leadStatusConverted = LeadStatus::query()->firstOrCreate(['name' => LeadStatus::STATUS_CONVERTED], [
            'name' => LeadStatus::STATUS_CONVERTED,
            'status' => User::STATUS_ACTIVE,
            'created_by' => 1,
        ]);

        $leadStatusCustomField = CustomField::query()->where('entity_type', Lead::class)
            ->where('code', 'lead-status')->first();
        CustomFieldValues::query()->updateOrCreate([
            'field_id' => $leadStatusCustomField->getKey(),
            'entity_id' => $lead->getKey(),
            'entity' => Lead::class,
        ], [
            'integer_value' => $leadStatusConverted->getKey(),
        ]);
    }

    /**
     * Workflow 1.2 from #58
     * @param Contact $contact
     * @return void
     */
    private function setContactAuthorityDefaultValue(Contact $contact, User $user): void
    {
        $contactAuthorityPayable = ContactAuthority::query()->where('name', 'Accounts Payable')->first();

        if ($contactAuthorityPayable) {
            CustomFieldValuesHelper::insertCustomFieldValue(
                'authority',
                $contactAuthorityPayable->getKey(),
                $contact->getKey(),
                Contact::class,
                $user,
            );
        }
    }

    /**
     * Workflow 2.1 from #58
     * @param Collection $leadCustomFields
     * @param Account $account
     * @param Contact $contact
     * @return void
     */
    private function setAccountAddressLeadAddressMissed(
        Collection $leadCustomFields,
        Account $account,
        Contact $contact,
        User $user,
    ): void {
        if (!isset($leadCustomFields['addresses']) || empty($leadCustomFields['addresses']->json_value)) {
            $contactAddresses = CustomFieldValuesHelper::getCustomFieldValues($account, ['addresses']);
            if (!empty($contactAddresses['addresses'])) {
                CustomFieldValuesHelper::insertCustomFieldValue(
                    'addresses',
                    $contactAddresses['addresses'],
                    $contact->getKey(),
                    Contact::class,
                    $user,
                );
            }
        }
    }
}
