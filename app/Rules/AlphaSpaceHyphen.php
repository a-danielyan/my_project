<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AlphaSpaceHyphen implements ValidationRule
{
    /**
     * Determine if the validation rule passes.
     * @param string $value
     * @return bool
     */
    public function passes(string $value): bool
    {
        return preg_match('/^[\pL\s-]+$/u', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute may only contain letters, space & hyphen.';
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->passes($value)) {
            $fail($this->message());
        }
    }
}
