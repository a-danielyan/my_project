<?php

namespace App\Http\RulesAttachments;

class Preset implements UserProfileStrategyInterface
{
    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'profile.id' => [
                'int',
                'exists:user_profile_image,id'
            ],
        ];
    }
}
