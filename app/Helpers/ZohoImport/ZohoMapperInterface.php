<?php

namespace App\Helpers\ZohoImport;

use App\Http\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

interface ZohoMapperInterface
{
    public function getMappingValues(): array;

    public function getEntityClassName(): string;

    public function getRepository(): BaseRepository;

    public function getInternalFields(array $zohoData, bool $isUpdate = false): array;

    public function afterInserted(Model $model, array $zohoData): void;
}
