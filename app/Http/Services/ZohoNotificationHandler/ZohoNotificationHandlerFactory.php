<?php

namespace App\Http\Services\ZohoNotificationHandler;

use App\Exceptions\CustomErrorException;

class ZohoNotificationHandlerFactory
{
    /**
     * @param string $entity
     * @return ZohoNotificationHandlerInterface
     * @throws CustomErrorException
     */
    public static function getNotificationHandlerForEntity(string $entity): ZohoNotificationHandlerInterface
    {
        return match ($entity) {
            'Leads' => resolve(LeadZohoNotificationHandler::class),
            'Accounts' => resolve(AccountsZohoNotificationHandler::class),
            'Contacts' => resolve(ContactsZohoNotificationHandler::class),
            'Deals' => resolve(DealsZohoNotificationHandler::class),
            'Quotes' => resolve(QuoteZohoNotificationHandler::class),
            'Tasks' => resolve(ActivityZohoNotificationHandler::class),
            'Products'=> resolve(ProductsZohoNotificationHandler::class),
            default => throw new CustomErrorException('Unknown ' . $entity . ' notification entity type', 422),
        };
    }
}
