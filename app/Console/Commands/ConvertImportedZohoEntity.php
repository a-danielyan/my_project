<?php

namespace App\Console\Commands;

use App\Exceptions\CustomErrorException;
use App\Helpers\ZohoImport\ZohoMapperFactory;
use App\Helpers\ZohoImport\ZohoMapperInterface;
use App\Http\Services\ZohoConverterService;
use App\Models\CustomField;
use App\Models\ZohoEntityExport;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class ConvertImportedZohoEntity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:convert-imported-zoho-entity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert imported zoho entity to our format';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        DB::connection()->disableQueryLog();
        if (config('app.env') !== 'testing') {
            DB::connection()->unsetEventDispatcher();
        }
        foreach (CreateZohoBulkImport::AVAILABLE_MOULES as $moduleAPIName) {
            echo 'Before module =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
            $this->convertItemsToOurFormat($moduleAPIName);
        }
    }


    private function convertItemsToOurFormat(string $moduleAPIName): void
    {
        $customFields = CustomField::query();
        /** @var ZohoMapperInterface $zohoConverter */
        try {
            $zohoConverter = ZohoMapperFactory::getMapperForEntity($moduleAPIName);
        } catch (CustomErrorException) {
            return;
        }

        $customFields = $customFields->where('entity_type', $zohoConverter->getEntityClassName())
            ->where('type', '!=', 'container')
            ->get()->keyBy('code');

        $zohoConverterService = new ZohoConverterService($zohoConverter, $customFields);

        ZohoEntityExport::query()->where('sync_status', ZohoEntityExport::STATUS_IN_PROGRESS)
            ->where('updated_at', '<', now()->subHour()->format('Y-m-d H:i:s'))
            ->update(['sync_status' => ZohoEntityExport::STATUS_NEW]);
        echo 'Before chunk =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
        ZohoEntityExport::query()->where('entity_type', $moduleAPIName)
            ->where('sync_status', ZohoEntityExport::STATUS_NEW)->chunkById(
                50,
                function (Collection $items) use (
                    $customFields,
                    $zohoConverter,
                    $zohoConverterService
                ) {
                    echo 'Chunk =' . memory_get_usage(true) . '/' . memory_get_usage() . PHP_EOL;
                    foreach ($items as $item) {
                        $item->sync_status = ZohoEntityExport::STATUS_IN_PROGRESS;
                        $item->save();

                        $zohoData = $item->data;
                        if (isset($zohoData['id'])) {
                            $zohoData['Id'] = $zohoData['id'];
                        }
                        try {
                            $zohoConverterService->insertZohoEntityToOurDatabase($zohoData);
                            $item->sync_status = ZohoEntityExport::STATUS_DONE;
                            $item->error = null;
                            $item->save();
                        } catch (Throwable $e) {
                            $item->sync_status = ZohoEntityExport::STATUS_ERROR;
                            $item->error = $e->getMessage();
                            $item->save();
                        }
                    }
                },
            );
    }
}
