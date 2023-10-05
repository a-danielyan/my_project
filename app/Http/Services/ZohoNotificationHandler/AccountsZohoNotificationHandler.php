<?php

namespace App\Http\Services\ZohoNotificationHandler;

use App\Http\Repositories\AccountRepository;
use App\Http\Services\AccountService;

class AccountsZohoNotificationHandler extends BaseZohoNotificationHandler
{
    public function __construct(AccountService $service, AccountRepository $repository)
    {
        parent::__construct($service, $repository);
    }

    public function moduleName(): string
    {
        return 'Accounts';
    }
}
