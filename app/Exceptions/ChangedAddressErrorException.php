<?php

namespace App\Exceptions;

use Exception;

class ChangedAddressErrorException extends Exception
{
    public function __construct(
        private ?array $changedAddresses,
        string $message = 'Address details changed',
        int $code = 409,
    ) {
        parent::__construct($message, $code);
    }

    public function getData(): array
    {
        return [
            'changedAddresses' => $this->changedAddresses,
        ];
    }
}
