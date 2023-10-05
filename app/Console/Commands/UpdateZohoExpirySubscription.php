<?php

namespace App\Console\Commands;

use App\Http\Services\ZohoService;
use App\Models\ZohoNotificationSubscription;
use com\zoho\crm\api\exception\SDKException;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateZohoExpirySubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-zoho-expiry-subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update subscription on zoho events';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        /** @var ZohoService $zohoService */
        $zohoService = resolve(ZohoService::class);

        $timeForUpdateEvents = now()->addHours(3);
        $eventsToWork = ZohoNotificationSubscription::query()->whereNull('expired_at')
            ->orWhere('expired_at', '<', $timeForUpdateEvents->format('Y-m-d H:i:s'))->get();

        foreach ($eventsToWork as $event) {
            /** @var ZohoNotificationSubscription $event */
            try {
                $notificationDetails = $zohoService->getNotification($event->chanel_id);
            } catch (SDKException $e) {
                Log::error(
                    'Cant get notification for existed event',
                    ['channelId' => $event->chanel_id, 'eventId' => $event->getKey(), 'error' => $e->getMessage()],
                );
            }


            if (!empty($notificationDetails['notifications'])) {
                // update expiration time
                try {
                    $channelExpiry = new DateTime();
                    $channelExpiry->modify('+23 hours');
                    $channelExpiry = $zohoService->updateChannelExpiratoryNotification(
                        $event->chanel_id,
                        $event->module,
                        $channelExpiry,
                    );
                    $event->expired_at = $channelExpiry->format('Y-m-d H:i:s');
                    $event->save();
                } catch (Throwable $e) {
                    Log::error(
                        'Cant update notification for event',
                        ['event' => $event->getKey(), 'error' => $e->getMessage()],
                    );
                }
            } else {
                //need create new notification
                try {
                    $channelExpiry = $zohoService->enableNotificationForModule($event->module, $event->chanel_id);
                    $event->expired_at = $channelExpiry->format('Y-m-d H:i:s');
                    $event->save();
                } catch (Throwable $e) {
                    Log::error(
                        'Cant recreate notification for event',
                        ['event' => $event->getKey(), 'error' => $e->getMessage()],
                    );
                }
            }
        }
    }
}
