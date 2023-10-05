<?php

namespace App\Http\RulesAttachments;

use Illuminate\Validation\Rule;

class Custom implements UserProfileStrategyInterface
{
    private const FILE_TYPES = ['jpeg', 'png', 'jpg'];

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'profile.name' => [
                'string',
                'nullable',
            ],
            'profile.extension' => [
                'required_with:profile.name',
                'string',
                Rule::in(self::FILE_TYPES),
            ],
            'profile.type' => [
                'required_with:profile.name',
                'string',
            ],
            'profile.size' => [
                'required_with:profile.name',
                'int',
            ],
            'profile.file_name' => [
                'required_with:profile.name',
                'string',
            ],
        ];
    }
}
