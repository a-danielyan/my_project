<?php

namespace App\Rules;

use App\Models\Contact;
use App\Models\Sequence\SequenceEntityAssociation;
use App\Models\Lead;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidateSequenceEntity implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value['type'] == 'Lead') {
            $entityType = Lead::class;
            $lead = Lead::query()->find($value['id']);
            if (!$lead) {
                $fail('Wrong Lead id');
            }
        } elseif ($value['type'] == 'Contact') {
            $entityType = Contact::class;
            $contact = Contact::query()->find($value['id']);
            if (!$contact) {
                $fail('Wrong Contact id');
            }
        } else {
            $fail('Wrong entity type ');
            return;
        }


        $count = SequenceEntityAssociation::query()->where('entity_id', $value['id'])
            ->where('entity_type', $entityType)->whereHas('sequence', function ($query) {
                $query->where('is_active', true);
            })->count();

        if ($count > 3) {
            $fail('Maximum of 3 active sequence allowed. ' . $value['type'] . ' ' . $value['id']);
        }
    }
}
