<?php

namespace App\Providers;

use App\Events\ModelChanged;
use App\Events\ModelCreated;
use App\Events\ModelDeleted;
use App\Events\ZohoAPIFail;
use App\Listeners\SendZohoAPIFAilNotification;
use App\Listeners\WriteModelChangedEntityLog;
use App\Listeners\WriteModelCreatedEntityLog;
use App\Listeners\WriteModelDeletedEntityLog;
use App\Models\CustomField;
use App\Models\Invoice;
use App\Observers\CustomFieldObserver;
use App\Observers\InvoiceObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ZohoAPIFail::class => [
            SendZohoAPIFAilNotification::class,
        ],
        ModelCreated::class => [
            WriteModelCreatedEntityLog::class,
        ],
        ModelChanged::class => [
            WriteModelChangedEntityLog::class,
        ],
        ModelDeleted::class => [
            WriteModelDeletedEntityLog::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        CustomField::observe(CustomFieldObserver::class);
        Invoice::observe(InvoiceObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
