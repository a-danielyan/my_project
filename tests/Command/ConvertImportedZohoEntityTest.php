<?php

use App\Console\Commands\ConvertImportedZohoEntity;
use App\Helpers\ZohoImport\SalesOrderZohoMapper;
use Mockery\MockInterface;
use Tests\TestCase;

class ConvertImportedZohoEntityTest extends TestCase
{
    /**
     * @return void
     */
    public function test_add_account_to_stripe(): void
    {
        $this->mock(
            SalesOrderZohoMapper::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('getEntityClassName')->andReturn('Sales_Order');;
            },
        );


        $this->artisan(ConvertImportedZohoEntity::class)->assertSuccessful();
    }
}
