<?php

namespace App\Http\Middleware;

use App\Exceptions\PermissionException;
use Closure;

class CheckAssistUserAccess
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws PermissionException
     */
    public function handle($request, Closure $next)
    {
        //@todo implement role based check
        return $next($request);
    }
}
