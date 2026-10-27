<?php

namespace App\Http\Middleware;

use App\Services\Readers\ReaderIdentity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Links in reader emails carry ?r={token}. Remember the reader in the
 * cookie and redirect to the same URL without the token, so it doesn't
 * stay in the address bar, history or analytics.
 */
class CaptureReaderToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') || ! $request->query->has('r')) {
            return $next($request);
        }

        $query = $request->query();
        $reader = ReaderIdentity::findByToken(is_string($query['r']) ? $query['r'] : null);
        unset($query['r']);

        $url = $request->url().($query ? '?'.http_build_query($query) : '');
        $response = redirect()->to($url, 302)->header('Cache-Control', 'no-store')->header('Referrer-Policy', 'no-referrer');

        return $reader ? $response->withCookie(ReaderIdentity::cookieFor($reader, $request)) : $response;
    }
}
