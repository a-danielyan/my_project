<?php

namespace App\Http\Services\ZohoNotificationHandler;

use App\Http\Repositories\EstimateRepository;
use App\Http\Services\EstimateService;

class QuoteZohoNotificationHandler extends BaseZohoNotificationHandler
{
    public function __construct(EstimateService $service, EstimateRepository $repository)
    {
        parent::__construct($service, $repository);
    }

    public function moduleName(): string
    {
        return 'Quotes';
    }
}
