<?php

namespace App\Models\Interfaces;

use App\Models\TusFileData;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * @property Collection<TusFileData>|TusFileData[] storedFiles
 */
interface StoreFileInterface
{
    /**
     * Relation with TusFileData
     *
     * @return MorphMany
     */
    public function storedFiles(): MorphMany;
}
