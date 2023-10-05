<?php

namespace App\Traits;

use App\Models\CustomField;
use Illuminate\Support\Facades\DB;

trait FactoryCustomFieldPropertyTrait
{
    private function getPropertyValue(CustomField $customField): array
    {
        switch ($customField->type) {
            case 'email':
                return ['text_value' => fake()->email];

            case 'phone':
                return ['text_value' => fake()->phoneNumber];

            case 'text':
                if ($customField->code == 'first-name') {
                    return ['text_value' => fake()->firstName];
                }
                if ($customField->code == 'last-name') {
                    return ['text_value' => fake()->lastName];
                }

                return ['text_value' => fake()->text(20)];

            case 'lookup':
                $record = DB::table($customField->lookup_type)->inRandomOrder()->first();
                if (!$record) {
                    return [];
                }

                return ['integer_value' => $record->id];

            case 'number':
                return ['float_value' => fake()->randomFloat(2, 1, 100)];
            default:
                return [];
        }
    }
}
