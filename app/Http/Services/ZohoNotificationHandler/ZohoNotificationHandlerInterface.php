<?php

namespace App\Http\Services\ZohoNotificationHandler;

interface ZohoNotificationHandlerInterface
{
    public function updateEntity(string $zohoEntityId, array $data): void;

    public function createEntity(array $data);

    public function moduleName(): string;
}
