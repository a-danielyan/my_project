<?php

namespace App\Exceptions;

use Throwable;

class ModelDeleteErrorException extends CustomErrorException
{
    public function __construct(string $message = 'Error delete model', int $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
