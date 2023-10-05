<?php

namespace App\Models\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface ModelWithContactInterface
{
    public function contact(): BelongsTo;
}
