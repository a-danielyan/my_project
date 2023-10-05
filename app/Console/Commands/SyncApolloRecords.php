<?php

namespace App\Console\Commands;

use App\Exceptions\ApolloRateLimitErrorException;
use App\Exceptions\CustomErrorException;
use App\Helpers\CommonHelper;
use App\Http\Services\AccountService;
use App\Http\Services\ContactService;
use App\Http\Services\LeadService;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Lead;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class SyncApolloRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-apollo-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data from apollo for new records';

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function handle(): void
    {
        Log::debug('Start SyncApolloRecords command');
        /** @var AccountService $accountService */
        $accountService = resolve(AccountService::class);

        $cronUser = CommonHelper::getCronUser();
        Account::query()->whereNull('apollo_synced_at')->chunkById(
            50,
            function (Collection $items) use ($accountService, $cronUser) {
                foreach ($items as $item) {
                    try {
                        $accountService->getDataFromApollo($item, $cronUser);
                        $item->apollo_synced_at = now();
                        $item->save();
                    } catch (ApolloRateLimitErrorException $e) {
                        die($e->getMessage());
                    } catch (CustomErrorException $e) {
                        Log::error(
                            'Custom Error when getting apollo data for account ' . $item->id . ' ' . $e->getMessage(),
                        );
                    }
                }
            },
        );

        /** @var ContactService $contactService */
        $contactService = resolve(ContactService::class);
        Contact::query()->whereNull('apollo_synced_at')->chunkById(
            50,
            function (Collection $items) use ($contactService, $cronUser) {
                foreach ($items as $item) {
                    try {
                        $contactService->getDataFromApollo($item, $cronUser);
                        $item->apollo_synced_at = now();
                        $item->save();
                    } catch (ApolloRateLimitErrorException $e) {
                        die($e->getMessage());
                    } catch (CustomErrorException $e) {
                        Log::error('Error when getting apollo data for contact ' . $item->id . ' ' . $e->getMessage());
                    }
                }
            },
        );

        /** @var LeadService $leadService */
        $leadService = resolve(LeadService::class);
        Lead::query()->whereNull('apollo_synced_at')->chunkById(
            50,
            function (Collection $items) use ($leadService, $cronUser) {
                foreach ($items as $item) {
                    try {
                        $leadService->getDataFromApollo($item, $cronUser);
                        $item->apollo_synced_at = now();
                        $item->save();
                    } catch (ApolloRateLimitErrorException $e) {
                        die($e->getMessage());
                    } catch (CustomErrorException $e) {
                        Log::error('Error when getting apollo data for lead ' . $item->id . ' ' . $e->getMessage());
                    }
                }
            },
        );
        Log::debug('End SyncApolloRecords command');
    }
}
