<?php

namespace App\Exceptions;

use Throwable;

class Oauth2TokenNotGeneratedException extends CustomErrorException
{
    public function __construct(
        string $message = 'Access token not generated',
        int $code = 422,
        Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
