<?php

namespace App\Exceptions;

use Throwable;

class ModelUpdateErrorException extends CustomErrorException
{
    public function __construct(string $message = 'Error update model', int $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
