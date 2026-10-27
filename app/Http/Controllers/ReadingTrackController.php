<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\Readers\ReaderIdentity;
use App\Services\Readers\ReadingTracker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Receives the sendBeacon pings from story pages: active seconds and max scroll.
 */
class ReadingTrackController extends Controller
{
    public function store(Request $request): Response
    {
        $reader = ReaderIdentity::current($request);

        if (! $reader) {
            return response()->noContent();
        }

        $data = $request->validate([
            'post_id' => ['required', 'integer'],
            'seconds' => ['required', 'integer', 'min:0'],
            'scroll' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $post = Post::query()->where('is_published', true)->find($data['post_id']);

        if ($post) {
            ReadingTracker::recordProgress($reader, $post, (int) $data['seconds'], (int) $data['scroll']);
        }

        return response()->noContent();
    }
}
