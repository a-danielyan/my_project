<?php

namespace App\Http\Resource;

use App\Models\PublicLinkAccessLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PublicLinkAccessLog
 */
class PublicLinkAccessLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return
            [
                'publicLinkToken' => $this->publicLink?->token,
                'ip' => $this->ip,
                'userAgent' => $this->user_agent,
                'clickedAt' => $this->created_at,
            ];
    }
}
