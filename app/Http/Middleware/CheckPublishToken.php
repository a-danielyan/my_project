<?php

namespace App\Http\Middleware;

use App\Exceptions\PermissionException;
use App\Http\Services\Publish\PublishTokenStrategy\PublishTokenService;
use App\Jobs\SavePublicLinkClickedLog;
use App\Models\PublishDetail;
use Closure;
use Illuminate\Http\Request;

class CheckPublishToken
{
    public function __construct(protected PublishTokenService $tokenService)
    {
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws PermissionException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $inputToken = $request->input('token');
        $entity = $request->input('entity');

        if ($this->tokenService->isTokenCan($inputToken, $entity)) {
            /** @var PublishDetail $token */
            $token = $this->tokenService->getTokenDetails($inputToken);
            $user = $token->user;

            $logData = [
                'publish_detail_id' => $token->getKey(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];
            SavePublicLinkClickedLog::dispatch($logData);

            if ($user) {
                auth()->login($user);

                return $next($request);
            }
        }

        throw new PermissionException();
    }
}
