<?php

namespace App\Exceptions;

use Throwable;

class InvalidMappingErrorException extends CustomErrorException
{
    public function __construct(
        string $message = 'Invalid Mapping value',
        int $code = 401,
        Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
