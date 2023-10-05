<?php

namespace App\Console\Commands;

use App\Helpers\StorageHelper;
use App\Models\TusFileData;
use App\Models\ZohoBulkImportJobs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ImportZohoBulkImportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-zoho-bulk-import-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import downloaded zoho file ';


    public function handle(): void
    {
        /** @var ZohoBulkImportJobs $jobToWork */
        $jobToWork = ZohoBulkImportJobs::query()
            ->where('status', ZohoBulkImportJobs::STATUS_DOWNLOADED)->first();
        if (!$jobToWork) {
            echo 'No jobs for work';

            return;
        }

        /** @var TusFileData $savedFile */
        $savedFile = TusFileData::query()->where('media_type', ZohoBulkImportJobs::class)
            ->where('media_id', $jobToWork->getKey())->first();

        if (!$savedFile) {
            Log::error('We cant find file for bulk import job', ['jobId' => $jobToWork->getKey()]);

            return;
        }
        try {
            $content = StorageHelper::getFileContent($savedFile);
        } catch (FileNotFoundException) {
            Log::error('We cant find file for bulk import job', ['jobId' => $jobToWork->getKey()]);

            return;
        }

        $temp = tempnam(sys_get_temp_dir(), 'myApp_');

        $fp = fopen($temp, 'w');

        fputs($fp, $content);
        fclose($fp);

        $fp = fopen(
            'zip://' . $temp . '#' . $jobToWork->job_id . '.csv',
            'r',
        );
        if (!$fp) {
            exit("cannot open\n");
        }

        $this->importRecords($fp, $jobToWork->module);
        $jobToWork->status = ZohoBulkImportJobs::STATUS_INSERTED;
        $jobToWork->save();
        fclose($fp);
    }

    private function importRecords($fp, string $moduleName): void
    {
        DB::connection()->disableQueryLog();
        if (config('app.env') !== 'testing') {
            DB::connection()->unsetEventDispatcher();
        }
        LazyCollection::make(function () use ($fp) {
            $headers = fgetcsv($fp, 0);
            while (($line = fgetcsv($fp, 0)) !== false) {
                $row = [];
                foreach ($line as $key => $item) {
                    if (!isset($headers[$key])) {
                        continue;
                    }
                    $row[$headers[$key]] = $item;
                }

                yield $row;
            }
        })
            ->chunk(1000)
            ->each(function (LazyCollection $chunk) use ($moduleName) {
                $records = $chunk->map(function ($row) use ($moduleName) {
                    return [
                        'entity_type' => $moduleName,
                        'entity_id' => $row['Id'],
                        'data' => json_encode($row),
                        'sync_status' => 'NEW',
                    ];
                })->toArray();

                DB::table('zoho_entity_exports')->insert($records);
            });
    }
}
