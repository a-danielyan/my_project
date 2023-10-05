<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class NotEmptyStringIdsList
 * For validating strings with ids, also check minimum one element
 * @package App\Rules
 */
class NotEmptyStringIdsList implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return $this->checkValidString($value) && $this->checkIfStringWithArrayNotEmpty($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute may contain array with numbers';
    }


    /**
     * @param string $value
     * @return bool
     */
    private function checkIfStringWithArrayNotEmpty(string $value): bool
    {
        return (bool) count(explode(',', $value));
    }

    /**
     * @param string $value
     * @return bool
     */
    private function checkValidString(string $value): bool
    {
        return (bool) preg_match('/^[\d\s,]*$/', $value);
    }
}
