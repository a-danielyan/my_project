<?php

namespace App\DTO;

class StripeCustomerDTO
{
    public ?string $description;
    public ?string $email;
    public ?array $metadata;
    public ?string $name;
    public ?string $phone;

    public function toStripeArray(): array
    {
        $stripeArray = [];

        foreach ($this as $key => $value) {
            if (!empty($value)) {
                $stripeArray[$key] = $value;
            }
        }

        return $stripeArray;
    }
}
