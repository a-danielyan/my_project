<?php

namespace App\Console\Commands;

use App\Models\ZohoEntityExport;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class GenerateBrokenSalesOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-broken-sales-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is one time dob to prepare CSV file with broken sales order details';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $brokenRecords = ZohoEntityExport::query()->where('error', 'Invoice id not found')
            ->orderBy('entity_id')->get();

        $brokenRecordsDetailsHeader = [
            'EntityId',
            'Amount',
            'Subject',
            'Due_Date',
            'SO_Number',
            'Order_Date_Date',
            'Account_Name',
        ];

        $fp = fopen(storage_path('broken_sales_order_missed_invoice.csv'), 'w');
        $this->saveDataToFile(
            $fp,
            $brokenRecordsDetailsHeader,
            $brokenRecords,
        );


        $brokenRecords = ZohoEntityExport::query()->where('error', 'Deals  with Id  not found')->orderBy(
            'entity_id',
        )->get();

        $fp = fopen(storage_path('broken_sales_order_missed_deals.csv'), 'w');
        $this->saveDataToFile(
            $fp,
            $brokenRecordsDetailsHeader,
            $brokenRecords,
        );
    }

    /**
     * @param $fp
     * @param array $brokenRecordsDetails
     * @param Collection|array $brokenRecords
     * @return void
     */
    protected function saveDataToFile(
        $fp,
        array $brokenRecordsDetails,
        Collection|array $brokenRecords,
    ): void {
        fputcsv($fp, $brokenRecordsDetails);

        foreach ($brokenRecords as $record) {
            $recordData = $record->data;

            $brokenRecordsDetails = [
                $recordData['Id'] ?? null,
                $recordData['Amount'] ?? null,
                $recordData['Subject'] ?? null,
                $recordData['Due_Date'] ?? null,
                $recordData['SO_Number'] ?? null,
                $recordData['Order_Date_Date'] ?? null,
                $recordData['Account_Name'] ?? null,
            ];
            fputcsv($fp, $brokenRecordsDetails);
        }
        fclose($fp);
    }
}
