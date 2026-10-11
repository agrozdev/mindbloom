<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GuardsAgainstBots;
use App\Mail\CommentSubmitted;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class PostCommentController extends Controller
{
    use GuardsAgainstBots;

    private const THANK_YOU = 'Благодарим ти! Коментарът ти е получен и ще се появи тук веднага щом го прочетем и одобрим.';

    public function store(Request $request, PostCategory $category, Post $post)
    {
        if ($this->isLikelyBot($request)) {
            return back()->withFragment('comments')->with('comment_status', self::THANK_YOU);
        }

        $data = $request->validateWithBag('comment', [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'body' => ['required', 'string', 'max:3000'],
            'challenge' => ['required', 'string', 'max:100'],
        ], [
            'name.required' => 'Моля, въведете вашето име.',
            'email.required' => 'Моля, въведете имейл адрес.',
            'email.email' => 'Моля, въведете валиден имейл адрес.',
            'body.required' => 'Моля, напишете коментара си.',
            'challenge.required' => 'Моля, отговорете на въпроса.',
            '*.max' => 'Полето е твърде дълго.',
        ]);

        $challenge = $post->commentChallenge();

        if ($challenge && ! $this->answersChallenge($data['challenge'], $challenge['word'])) {
            return back()
                ->withFragment('comments')
                ->withInput($request->except('challenge'))
                ->withErrors(['challenge' => 'Хм, това не е точната дума. Ето още една буква за подсказка.'], 'comment')
                ->with('comment_hint_letters', 2);
        }

        $comment = $post->comments()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'body' => $data['body'],
            'ip_address' => $request->ip(),
        ]);

        try {
            Mail::to(config('mail.comment_moderators'))->send(new CommentSubmitted($comment));
        } catch (Throwable $e) {
            report($e);
        }

        return back()->withFragment('comments')->with('comment_status', self::THANK_YOU);
    }

    private function answersChallenge(string $answer, string $word): bool
    {
        $normalize = fn (string $value) => mb_strtolower(trim($value, " \t\n\r\0\x0B.,!?;:\"'„“”«»"));

        return $normalize($answer) === $normalize($word);
    }
}
