<?php

namespace App\Http\Requests\Traits;

trait MultiSelectTrait
{
    protected function prepareMultiSelectForValidation(): void
    {
        $mergeArray = [];
        foreach (static::MULTI_SELECT_FIELDS as $field) {
            if ($this->query($field) && !is_array($this->query($field))) {
                $mergeArray[$field] = explode(',', $this->query($field));
            }
        }
        $this->merge($mergeArray);
    }
}
