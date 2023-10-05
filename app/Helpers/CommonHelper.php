<?php

namespace App\Helpers;

use App\Models\Account;
use App\Models\Contact;
use App\Models\CustomField;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;

class CommonHelper
{
    /**
     * get model name from path
     *
     * @param string $modelName
     * @return string
     */
    public static function modelName(string $modelName): string
    {
        $modelArray = explode('\\', $modelName);

        return strtolower(ltrim($modelArray[array_key_last($modelArray)], 'Org'));
    }

    /**
     * @param string $url
     * @return string
     * @throws GuzzleException
     */
    public static function fileGetContentsGuzzle(string $url): string
    {
        $client = new Client([
            'timeout' => 10,
        ]);
        $response = $client->get($url);

        return (string)$response->getBody();
    }

    public static function getCronUser(): User
    {
        return Cache::remember('default_cron_user', 3600, function () {
            /** @var User $cronUser */
            $cronUser = User::query()->withTrashed()->where('email', User::EMAIL_FOR_CRON_USER)->first();
            if (!$cronUser) {
                $cronUser = User::query()->withTrashed()->find(1);
            }

            return $cronUser;
        });
    }

    public static function getOrCreateDefaultAccount()
    {
        $customFieldAccountName = CustomField::query()->where('code', 'account-name')
            ->where('entity_type', Account::class)->first();
        $account = Account::query()->whereHas('customFields', function ($query) use ($customFieldAccountName) {
            $query->where('field_id', $customFieldAccountName->getKey())->where(
                'text_value',
                Account::DEFAULT_ACCOUNT_NAME,
            );
        })->first();

        if (!$account) {
            $cronUser = CommonHelper::getCronUser();
            $account = Account::query()->create([
                'created_by' => $cronUser->getKey(),
            ]);

            CustomFieldValuesHelper::insertCustomFieldValue(
                'account-name',
                Account::DEFAULT_ACCOUNT_NAME,
                $account->getKey(),
                Account::class,
                $cronUser
            );
        }

        return $account;
    }

    public static function getOrCreateDefaultContactForAccount(int $accountId)
    {
        $cronUser = CommonHelper::getCronUser();

        $customFieldContactFirstName = Cache::remember('contact_first_name', 3600, function () {
            return CustomField::query()->where('code', 'first-name')
                ->where('entity_type', Contact::class)->first();
        });

        $customFieldContactLastName = Cache::remember('contact_last_name', 3600, function () {
            return CustomField::query()->where('code', 'last-name')
                ->where('entity_type', Contact::class)->first();
        });

        $contact = Contact::query()->whereHas('customFields', function ($query) use ($customFieldContactLastName) {
            $query->where('field_id', $customFieldContactLastName->getKey())->where(
                'text_value',
                Contact::DEFAULT_CONTACT_LAST_NAME,
            );
        })->whereHas('customFields', function ($query) use ($customFieldContactFirstName) {
            $query->where('field_id', $customFieldContactFirstName->getKey())->where(
                'text_value',
                Contact::DEFAULT_CONTACT_FIRST_NAME,
            );
        })->where('account_id', $accountId)->first();

        if (!$contact) {
            $contact = Contact::query()->create([
                'created_by' => $cronUser->getKey(),
                'account_id' => $accountId,
            ]);

            CustomFieldValuesHelper::insertCustomFieldValue(
                'contact_first_name',
                Contact::DEFAULT_CONTACT_FIRST_NAME,
                $contact->getKey(),
                Contact::class,
                $cronUser
            );

            CustomFieldValuesHelper::insertCustomFieldValue(
                'contact_last_name',
                Contact::DEFAULT_CONTACT_LAST_NAME,
                $contact->getKey(),
                Contact::class,
                $cronUser
            );
        }

        return $contact;
    }
}
