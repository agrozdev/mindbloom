<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        // Static pages.
        $static = [
            ['home', 'weekly', '1.0'],
            ['about', 'monthly', '0.8'],
            ['services.index', 'monthly', '0.9'],
            ['events.index', 'weekly', '0.7'],
            ['blog.index', 'weekly', '0.7'],
            ['contact', 'yearly', '0.6'],
            ['legal.privacy', 'yearly', '0.3'],
            ['legal.terms', 'yearly', '0.3'],
            ['legal.cookies', 'yearly', '0.3'],
        ];
        foreach ($static as [$route, $changefreq, $priority]) {
            $urls[] = ['loc' => route($route), 'lastmod' => null, 'changefreq' => $changefreq, 'priority' => $priority];
        }

        foreach (Service::active()->get() as $service) {
            $urls[] = [
                'loc' => route('services.show', $service),
                'lastmod' => $service->updated_at,
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ];
        }

        foreach (Event::active()->get() as $event) {
            $urls[] = [
                'loc' => route('events.show', $event),
                'lastmod' => $event->updated_at,
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        $categories = PostCategory::withMax('posts', 'updated_at')->has('posts')->get();
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => route('blog.category', $category),
                'lastmod' => $category->posts_max_updated_at ?: $category->updated_at,
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ];
        }

        Post::published()->with('category')->get()
            ->filter(fn (Post $post) => $post->category !== null)
            ->each(function (Post $post) use (&$urls) {
                $urls[] = [
                    'loc' => route('blog.show', [$post->category, $post]),
                    'lastmod' => $post->updated_at,
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            });

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
