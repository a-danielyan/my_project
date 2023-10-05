<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMailgunWebhook
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->isDataValid($request)) {
            abort(422);
        }

        return $next($request);
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function isDataValid(Request $request): bool
    {
        $webhookKey = config('services.mailgun.webhookKey');
        $signature = $request->get('signature');

        if (abs(time() - $signature['timestamp']) > 15) {
            return false;
        }

        return hash_equals(
            hash_hmac('sha256', $signature['timestamp'] . $signature['token'], $webhookKey),
            $signature['signature'],
        );
    }
}
