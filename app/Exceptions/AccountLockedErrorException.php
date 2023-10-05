<?php

namespace App\Exceptions;

use Throwable;

/**
 * Class AccountLockedErrorException
 * @package App\Exceptions
 */
class AccountLockedErrorException extends CustomErrorException
{
    public function __construct(
        string $message = 'Account locked. Please click the \'Forgot Password\' link to reset your password',
        int $code = 429,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
