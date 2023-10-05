<?php

namespace App\Http\Services\ZohoNotificationHandler;

use App\Http\Repositories\LeadRepository;
use App\Http\Services\LeadService;

class LeadZohoNotificationHandler extends BaseZohoNotificationHandler
{
    public function __construct(LeadService $service, LeadRepository $repository)
    {
        parent::__construct($service, $repository);
    }

    public function moduleName(): string
    {
        return 'Leads';
    }
}
