<?php

namespace App\Http\RulesAttachments;

use Illuminate\Validation\Rule;

class Base implements UserProfileStrategyInterface
{
    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'profile' => [
                'array',
                'nullable'
            ],
            'profile.name' => [
                Rule::requiredIf(fn() => is_array(request()->profile) && empty(request()->profile['id'])),
                'prohibited_unless:profile.id,null'
            ],
            'profile.id' => [
                Rule::requiredIf(fn() => is_array(request()->profile) && empty(request()->profile['name'])),
                'prohibited_unless:profile.name,null',
            ]
        ];
    }
}
