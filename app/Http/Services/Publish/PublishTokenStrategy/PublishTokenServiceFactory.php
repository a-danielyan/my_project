<?php

namespace App\Http\Services\Publish\PublishTokenStrategy;

use App\Exceptions\NotFoundException;
use App\Models\PublishDetail;

class PublishTokenServiceFactory
{
    /**
     * @param string $entity
     * @return PublishTokenServiceInterface
     * @throws NotFoundException
     */
    public static function getService(string $entity): PublishTokenServiceInterface
    {
        return match ($entity) {
            PublishDetail::ENTITY_TYPE_PROPOSAL => resolve(PublishTokenService::class),
            default => throw new NotFoundException('Unknown Entity name')
        };
    }
}
