<?php

namespace App\Http\Middleware;

use App\Exceptions\PermissionException;
use Closure;

class CheckUserStatus
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws PermissionException
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->status === 'Active') {
            return $next($request);
        }

        throw new PermissionException();
    }
}
