<?php

namespace App\Exceptions;

use Throwable;

/**
 * Class ModelTooManyAttemptsErrorException
 * @package App\Exceptions
 */
class ModelTooManyAttemptsErrorException extends CustomErrorException
{
    public function __construct(
        string $message = 'You\'ve made too many attempts, please retry later.',
        int $code = 429,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
