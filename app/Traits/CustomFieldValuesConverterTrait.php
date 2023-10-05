<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait CustomFieldValuesConverterTrait
{
    protected function convertCustomFieldValuesToKeyValueWithId(Collection $customFieldValues): Collection
    {
        return $customFieldValues->mapWithKeys(function ($item) {
            if ($item->customField) {
                return [$item->customField->code => ['value' => $item->text_value, 'id' => $item->id]];
            }

            return [];
        });
    }
}
