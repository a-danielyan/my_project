<?php

namespace App\Exceptions;

use Throwable;

class ModelCreateErrorException extends CustomErrorException
{
    public function __construct(string $message = 'Error create model', int $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
