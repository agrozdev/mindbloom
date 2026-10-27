<?php

namespace App\Http\Controllers;

use App\Services\Readers\ReaderIdentity;
use Illuminate\Http\Request;

/**
 * /otpisvane/{token}. GET shows a button rather than unsubscribing, because
 * mail scanners open every link in an email. POST unsubscribes: from the
 * button, or straight from Gmail/Outlook via List-Unsubscribe-Post
 * (CSRF-exempt; the token is the proof).
 */
class UnsubscribeController extends Controller
{
    public function show(string $token)
    {
        $reader = ReaderIdentity::findByToken($token);

        return view('readers.unsubscribe', [
            'reader' => $reader,
            'token' => $token,
            'done' => $reader?->isUnsubscribed() ?? false,
        ]);
    }

    public function store(Request $request, string $token)
    {
        $reader = ReaderIdentity::findByToken($token);

        if ($reader && ! $reader->isUnsubscribed()) {
            $reader->forceFill(['unsubscribed_at' => now()])->save();
        }

        if (! $reader) {
            return response()->view('readers.unsubscribe', ['reader' => null, 'token' => $token, 'done' => false], 404);
        }

        return view('readers.unsubscribe', ['reader' => $reader, 'token' => $token, 'done' => true]);
    }
}
