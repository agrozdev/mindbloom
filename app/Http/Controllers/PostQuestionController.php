<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GuardsAgainstBots;
use App\Mail\PostQuestionSubmitted;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PostQuestionController extends Controller
{
    use GuardsAgainstBots;

    public function store(Request $request, PostCategory $category, Post $post)
    {
        if ($this->isLikelyBot($request)) {
            return back()->with('question_status', 'Благодарим ви! Ще ви отговорим възможно най-скоро.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'question' => ['required', 'string', 'max:5000'],
        ], [
            'name.required' => 'Моля, въведете вашето име.',
            'email.required' => 'Моля, въведете имейл адрес.',
            'email.email' => 'Моля, въведете валиден имейл адрес.',
            'question.required' => 'Моля, въведете въпроса си.',
            '*.max' => 'Полето е твърде дълго.',
        ]);

        Mail::to(config('mail.admin_address'))->send(new PostQuestionSubmitted($post, $data));

        return back()->with('question_status', 'Благодарим ви! Ще ви отговорим възможно най-скоро.');
    }
}
