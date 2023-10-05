<?php

namespace App\Exceptions;

use Throwable;

class FileUploadException extends CustomErrorException
{
    public function __construct(
        string $message = 'Error file upload',
        int $code = 400,
        Throwable $previous = null,
    ) {
        parent::__construct(
            $message,
            $code,
            $previous,
        );
    }
}
