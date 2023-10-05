<?php

namespace App\Exceptions;

use Throwable;

/**
 * Class CredentialInvalidErrorException
 * @package App\Exceptions
 */
class CredentialInvalidErrorException extends CustomErrorException
{
    public function __construct(
        string $message = 'Credentials Invalid',
        int $code = 401,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
