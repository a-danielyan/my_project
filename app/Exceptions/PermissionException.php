<?php

namespace App\Exceptions;

use Throwable;

class PermissionException extends CustomErrorException
{
    public function __construct(
        string $message = 'You don\'t have enough permission',
        int $code = 403,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
