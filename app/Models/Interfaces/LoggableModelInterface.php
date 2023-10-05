<?php

namespace App\Models\Interfaces;

/**
 * Interface LoggableModelInterface
 * @package App\Models\Interfaces
 */
interface LoggableModelInterface
{
    /**
     * Log component key
     *
     * @return string
     */
    public static function getLoggableName(): string;
}
