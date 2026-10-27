<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Models\Post;
use App\Models\Review;
use App\Services\Readers\ReaderIdentity;
use Illuminate\Http\Request;

/**
 * The review page linked from feedback emails: /otziv/{token}.
 */
class FeedbackController extends Controller
{
    public function show(Request $request, string $token)
    {
        $log = $this->findLog($token);

        if (! $log) {
            return response()->view('readers.feedback-invalid', [], 404);
        }

        $rating = (int) $request->query('rating');

        // Opening a personal link identifies the reader, like ?r= does.
        return response()
            ->view('readers.feedback', [
                'log' => $log,
                'post' => $log->post,
                'reader' => $log->reader,
                'token' => $token,
                'rating' => old('rating', $rating >= 1 && $rating <= 5 ? $rating : null),
                'alreadySent' => $log->review()->exists(),
            ])
            ->withCookie(ReaderIdentity::cookieFor($log->reader, $request));
    }

    /**
     * Where the feedback links in test emails lead: the real form, filled
     * for the admin to look at, that saves nothing.
     */
    public function demo(Request $request, Post $post)
    {
        $rating = (int) $request->query('rating');

        return response()->view('readers.feedback', [
            'demo' => true,
            'post' => $post->load('category'),
            'reader' => null,
            'token' => null,
            'rating' => $rating >= 1 && $rating <= 5 ? $rating : null,
            'alreadySent' => false,
        ]);
    }

    public function store(Request $request, string $token)
    {
        $log = $this->findLog($token);

        if (! $log) {
            return response()->view('readers.feedback-invalid', [], 404);
        }

        if ($log->review()->exists()) {
            return redirect()->route('feedback.show', $token);
        }

        if (filled($request->input('website'))) {
            return redirect()->route('feedback.show', $token)->with('feedback_status', true);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'answer_feeling' => ['nullable', 'string', 'max:3000'],
            'answer_lesson' => ['nullable', 'string', 'max:3000'],
            'can_publish' => ['nullable', 'boolean'],
        ], [
            'rating.required' => 'Моля, изберете оценка от 1 до 5 звезди.',
            'rating.*' => 'Моля, изберете оценка от 1 до 5 звезди.',
            '*.max' => 'Текстът е твърде дълъг.',
        ]);

        Review::query()->create([
            'reader_id' => $log->reader_id,
            'post_id' => $log->post_id,
            'email_log_id' => $log->id,
            'rating' => $data['rating'],
            'answer_feeling' => trim((string) ($data['answer_feeling'] ?? '')) ?: null,
            'answer_lesson' => trim((string) ($data['answer_lesson'] ?? '')) ?: null,
            'can_publish' => $request->boolean('can_publish'),
            'status' => Review::STATUS_PENDING,
        ]);

        return redirect()->route('feedback.show', $token)->with('feedback_status', true);
    }

    /**
     * Only emails that were really sent and are about a story that still exists.
     */
    private function findLog(string $token): ?EmailLog
    {
        if (! ReaderIdentity::isWellFormed($token)) {
            return null;
        }

        $log = EmailLog::query()
            ->with(['reader', 'post.category'])
            ->where('feedback_token', $token)
            ->where('status', EmailLog::STATUS_SENT)
            ->first();

        return $log && $log->post && $log->reader && hash_equals($log->feedback_token, $token) ? $log : null;
    }
}
