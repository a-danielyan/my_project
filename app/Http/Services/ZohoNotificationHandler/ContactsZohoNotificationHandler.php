<?php

namespace App\Http\Services\ZohoNotificationHandler;

use App\Http\Repositories\ContactRepository;
use App\Http\Services\ContactService;

class ContactsZohoNotificationHandler extends BaseZohoNotificationHandler
{
    public function __construct(ContactService $service, ContactRepository $repository)
    {
        parent::__construct($service, $repository);
    }

    public function moduleName(): string
    {
        return 'Contacts';
    }
}
