<?php

namespace App\Http\RequestTransformers\Auth;

use App\Http\RequestTransformers\AbstractRequestTransformer;
use Illuminate\Http\Request;

/**
 * Class AuthWithSocialTransformer
 * @package App\Http\RequestTransformers\Common\Auth
 */
class AuthWithSocialTransformer extends AbstractRequestTransformer
{
    /**
     * To map fields
     *
     * @return array
     */
    protected function getMap(): array
    {
        return [
            'provider' => 'provider',
            'tokenId' => 'tokenId',
            'token' => 'token',
            'code'  => 'code'
        ];
    }

    public function transform(Request $request): array
    {
        $transformed = parent::transform($request);

        foreach ($transformed as $key => $value) {
            if (isset($this->getValueMap()[$value])) {
                $transformed[$key] = $this->getValueMap()[$value];
            }
        }

        return $transformed;
    }

    /**
     * @return string[]
     */
    private function getValueMap(): array
    {
        return [
            'microsoft' => 'graph',
            'graph' => 'graph',
            'facebook' => 'facebook',
            'linkedin' => 'linkedin',
            'google' => 'google',
            'okta' => 'okta',
        ];
    }
}
