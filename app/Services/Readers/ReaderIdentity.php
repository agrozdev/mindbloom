<?php

namespace App\Services\Readers;

use App\Models\Reader;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Identifies readers by a random token kept in an HttpOnly cookie. The
 * cookie holds only the token, never the name or email.
 */
class ReaderIdentity
{
    public const COOKIE = 'mb_reader';

    private const LIFETIME_MINUTES = 60 * 24 * 365;

    public static function newToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public static function isWellFormed(?string $token): bool
    {
        return is_string($token) && preg_match('/^[a-f0-9]{64}$/', $token) === 1;
    }

    public static function findByToken(?string $token): ?Reader
    {
        if (! self::isWellFormed($token)) {
            return null;
        }

        $reader = Reader::query()->where('token', $token)->first();

        return $reader && hash_equals($reader->token, $token) ? $reader : null;
    }

    /**
     * The reader behind the request's cookie, looked up once per request.
     */
    public static function current(Request $request): ?Reader
    {
        if (! $request->attributes->has('reader')) {
            $request->attributes->set('reader', self::findByToken($request->cookie(self::COOKIE)));
        }

        return $request->attributes->get('reader');
    }

    public static function cookieFor(Reader $reader, Request $request): Cookie
    {
        return cookie(
            self::COOKIE,
            $reader->token,
            self::LIFETIME_MINUTES,
            path: '/',
            secure: $request->isSecure() || app()->isProduction(),
            httpOnly: true,
            sameSite: 'lax',
        );
    }
}
