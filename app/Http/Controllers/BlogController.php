<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Setting;
use App\Services\Readers\ReaderIdentity;
use App\Services\Readers\ReadingTracker;
use App\Services\Readers\StoryGate;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        return view('blog.categories', [
            'categories' => PostCategory::withCount(['posts' => fn ($query) => $query->published()])
                ->with(['posts' => fn ($query) => $query->published()])
                ->orderBy('name')
                ->get(),
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
        $showFullContent = ! $isLocked || $this->hasValidUnlock($post, $request->query('unlock'));
        $reader = ReaderIdentity::current($request);

        if ($reader && $showFullContent) {
            ReadingTracker::recordVisit($reader, $post);
        }

        // The whole story is always in the HTML (for search engines); the gate
        // only hides the locked part with CSS until the reader signs up.
        $gate = $showFullContent && $post->isGated()
            ? StoryGate::split($post->body, (int) Setting::get('gate_visible_percent'))
            : null;

        $response = response()->view('blog.show', [
            'post' => $post,
            'showFullContent' => $showFullContent,
            'gate' => $gate,
            'readerIdentified' => $reader !== null,
            'reviews' => $post->reviews()->published()->with('reader')->latest()->get(),
            'relatedPosts' => $this->relatedPosts($post),
            'comments' => $post->comments()->approved()->orderBy('approved_at')->get(),
            'commentChallenge' => $post->commentChallenge(),
        ]);

        // The page differs per reader (gate or no gate), so keep shared caches out.
        return $reader ? $response->header('Cache-Control', 'private, no-store') : $response;
    }

    /**
     * Up to 6 other posts from the same category: the neighbours around the current
     * post (by publish date, wrapping around), topped up with the newest posts when
     * the category is small. Using neighbours instead of the same newest posts on
     * every page spreads internal links evenly, so Google can find and index every post.
     */
    private function relatedPosts(Post $post)
    {
        $siblings = Post::published()
            ->with('category')
            ->where('category_id', $post->category_id)
            ->get();

        $others = $siblings->reject(fn ($sibling) => $sibling->id === $post->id)->values();

        if ($others->count() > 6) {
            $position = $siblings->search(fn ($sibling) => $sibling->id === $post->id);
            $position = $position === false ? 0 : $position;
            $count = $siblings->count();

            $related = collect([-3, -2, -1, 1, 2, 3])
                ->map(fn ($offset) => $siblings[(($position + $offset) % $count + $count) % $count]);
        } else {
            $related = $others;
        }

        if ($related->count() < 6) {
            $related = $related->concat(
                Post::published()
                    ->with('category')
                    ->where('id', '!=', $post->id)
                    ->whereNotIn('id', $related->pluck('id'))
                    ->limit(6 - $related->count())
                    ->get()
            );
        }

        return $related->values();
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
