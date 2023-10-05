<?php

namespace App\Http\Services\ZohoNotificationHandler;

use App\Http\Repositories\OpportunityRepository;
use App\Http\Services\OpportunityService;

class DealsZohoNotificationHandler extends BaseZohoNotificationHandler
{
    public function __construct(OpportunityService $service, OpportunityRepository $repository)
    {
        parent::__construct($service, $repository);
    }

    public function moduleName(): string
    {
        return 'Deals';
    }
}
