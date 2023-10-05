<?php

namespace App\DTO;

class StripeProductDTO
{
    public string $name;
    public ?string $description;
    public ?array $metadata;

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
