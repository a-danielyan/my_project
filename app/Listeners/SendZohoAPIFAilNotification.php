<?php

namespace App\Listeners;

use App\Events\ZohoAPIFail;
use App\Mail\ZohoAPIError;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendZohoAPIFAilNotification implements ShouldQueue
{
    private const ADMINISTRATOR_LIST_FOR_NOTIFICATION = [
        'r.hovtvian@gmail.com',
    ];

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ZohoAPIFail $event): void
    {
        foreach (self::ADMINISTRATOR_LIST_FOR_NOTIFICATION as $email) {
            if (config('app.env') === 'local') {
                continue;
            }
            Mail::to($email)->send(
                new ZohoAPIError(
                    $event->errorMsg,
                ),
            );
        }
    }

    public function failed(ZohoAPIFail $event, Throwable $exception): void
    {
        Log::error('Zoho API fails. ' . $event->errorMsg . ' Cant process notification ' . $exception->getMessage());
    }
}
