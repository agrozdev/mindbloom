<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        return view('blog.categories', [
            'categories' => PostCategory::withCount(['posts' => fn ($query) => $query->published()])->orderBy('name')->get(),
        ]);
    }

    public function category(PostCategory $category)
    {
        return view('blog.category', [
            'posts' => $category->posts()->published()->paginate(9),
            'categories' => PostCategory::orderBy('name')->get(),
            'activeCategory' => $category,
        ]);
    }

    public function show(PostCategory $category, Post $post, Request $request)
    {
        $isLocked = $post->price !== null;

        return view('blog.show', [
            'post' => $post,
            'showFullContent' => ! $isLocked || $this->hasValidUnlock($post, $request->query('unlock')),
        ]);
    }

    private function hasValidUnlock(Post $post, ?string $token): bool
    {
        if (! $token) {
            return false;
        }

        return Order::query()
            ->where('uuid', $token)
            ->where('orderable_type', Post::class)
            ->where('orderable_id', $post->id)
            ->where('status', Order::STATUS_PAID)
            ->exists();
    }
}
