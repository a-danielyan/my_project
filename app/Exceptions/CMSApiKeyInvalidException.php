<?php

namespace App\Exceptions;

use Throwable;

class CMSApiKeyInvalidException extends CustomErrorException
{
    public function __construct(
        string $message = 'You credentials invalid',
        int $code = 403,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
