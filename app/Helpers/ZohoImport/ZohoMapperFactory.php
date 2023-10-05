<?php

namespace App\Helpers\ZohoImport;

use App\Exceptions\CustomErrorException;

class ZohoMapperFactory
{
    /**
     * @param string $entity
     * @return ZohoMapperInterface
     * @throws CustomErrorException
     */
    public static function getMapperForEntity(string $entity): ZohoMapperInterface
    {
        return match ($entity) {
            'Leads' => resolve(LeadZohoMapper::class),
            'Accounts' => resolve(AccountsZohoMapper::class),
            'Contacts' => resolve(ContactsZohoMapper::class),
            'Deals' => resolve(DealsZohoMapper::class),
            'Quotes' => resolve(QuoteZohoMapper::class),
            'Tasks' => resolve(ActivityZohoMapper::class),
            'Calls' => resolve(CallsZohoMapper::class),
            'Products' => resolve(ProductsZohoMapper::class),
            'Sales_Orders' => resolve(SalesOrderZohoMapper::class),
            default => throw new CustomErrorException('Unknown entity type', 422),
        };
    }
}
