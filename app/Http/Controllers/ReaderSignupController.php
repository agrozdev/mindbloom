<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Reader;
use App\Models\Setting;
use App\Services\Readers\ReaderIdentity;
use App\Services\Readers\ReadingTracker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * The story gate form: stores the reader and unlocks the story via AJAX.
 */
class ReaderSignupController extends Controller
{
    /** Bump when the consent text in the gate changes. */
    public const CONSENT_VERSION = '2026-10-27';

    public function store(Request $request): JsonResponse
    {
        // Honeypot: pretend it worked, but store nothing and set no cookie.
        if (filled($request->input('website'))) {
            return response()->json(['ok' => true]);
        }

        $data = $request->validate([
            'post_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'consent' => ['accepted'],
        ], [
            'name.required' => 'Моля, въведете името си.',
            'name.max' => 'Името е твърде дълго.',
            'email.required' => 'Моля, въведете имейл адрес.',
            'email.email' => 'Моля, въведете валиден имейл.',
            'email.max' => 'Имейлът е твърде дълъг.',
            'consent.accepted' => 'Моля, потвърдете съгласието си.',
            'post_id.*' => 'Възникна грешка. Моля, презаредете страницата.',
        ]);

        $post = Post::query()->where('is_published', true)->find($data['post_id']);

        if (! $post) {
            return response()->json(['message' => 'Възникна грешка. Моля, презаредете страницата.'], 422);
        }

        if (! $this->passesTurnstile($request)) {
            return response()->json(['errors' => ['turnstile' => ['Проверката за сигурност не успя. Моля, презаредете страницата и опитайте отново.']]], 422);
        }

        $email = Reader::normalizeEmail($data['email']);
        $reader = Reader::query()->where('email', $email)->first() ?? new Reader([
            'email' => $email,
            'token' => ReaderIdentity::newToken(),
            'signup_story_id' => $post->id,
        ]);

        if (blank($reader->name)) {
            $reader->name = trim($data['name']);
        }

        // Ticking the box again is a fresh consent, also for someone who had unsubscribed.
        $reader->fill([
            'consent_at' => now(),
            'consent_version' => self::CONSENT_VERSION,
            'consent_ip' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 500),
            'unsubscribed_at' => null,
        ])->save();

        // The page was rendered before the reader was known, so count this view now.
        ReadingTracker::recordVisit($reader, $post);

        return response()->json(['ok' => true])->withCookie(ReaderIdentity::cookieFor($reader, $request));
    }

    private function passesTurnstile(Request $request): bool
    {
        $secret = Setting::get('turnstile_secret_key');

        if (blank($secret) || blank(Setting::get('turnstile_site_key'))) {
            return true;
        }

        try {
            return (bool) Http::asForm()->timeout(10)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => (string) $request->input('cf-turnstile-response'),
                'remoteip' => $request->ip(),
            ])->json('success');
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }
}
