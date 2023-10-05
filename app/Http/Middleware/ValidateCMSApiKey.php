<?php

namespace App\Http\Middleware;

use App\Exceptions\CMSApiKeyInvalidException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCMSApiKey
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws CMSApiKeyInvalidException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->api_key !== config('auth.cms_api_key')) {
            throw new CMSApiKeyInvalidException();
        }

        return $next($request);
    }
}
