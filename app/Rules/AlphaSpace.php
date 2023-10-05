<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AlphaSpace implements ValidationRule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $value
     * @return bool
     */
    public function passes(string $value): bool
    {
        // This will only accept alpha and spaces.
        // If you want to accept hyphens use: /^[\pL\s-]+$/u.
        return preg_match('/^[\pL\'\s]+$/u', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute may only contain letters & space.';
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->passes($value)) {
            $fail($this->message());
        }
    }
}
