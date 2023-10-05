<?php

namespace App\Http\Services\ZohoNotificationHandler;

use App\Http\Repositories\ProductRepository;
use App\Http\Services\ProductService;

class ProductsZohoNotificationHandler extends BaseZohoNotificationHandler implements ZohoNotificationHandlerInterface
{
    public function __construct(private ProductService $service, private ProductRepository $repository)
    {
        parent::__construct($service, $repository);
    }

    public function moduleName(): string
    {
        return 'Products';
    }
}
