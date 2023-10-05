<?php

namespace App\Models\Interfaces;

use App\Http\Filter\AbstractFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Interface FilteredInterface
 * @package App\Models\Interfaces
 *
 * @method static Builder filter(array $params)
 */
interface FilterStrategy extends FilteredInterface
{
    public function filterStrategy(): AbstractFilter;
}
