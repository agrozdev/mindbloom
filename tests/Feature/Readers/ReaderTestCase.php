<?php

namespace Tests\Feature\Readers;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Reader;
use App\Services\Readers\ReaderIdentity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class ReaderTestCase extends TestCase
{
    use RefreshDatabase;

    protected function makeStory(array $attributes = []): Post
    {
        $category = PostCategory::query()->firstOrCreate(['slug' => 'prikazki'], ['name' => 'Приказки']);

        $paragraphs = collect(range(1, 10))
            ->map(fn (int $i) => '<p>Параграф '.$i.' от приказката, в който се случва нещо вълшебно и важно.</p>')
            ->implode('');

        return Post::query()->create(array_merge([
            'title' => 'Слънчевият лъч',
            'slug' => 'slanchevia-lach-'.uniqid(),
            'body' => $paragraphs,
            'category_id' => $category->id,
            'published_at' => now()->subDay(),
            'is_published' => true,
        ], $attributes))->load('category');
    }

    protected function makeReader(array $attributes = []): Reader
    {
        return Reader::query()->create(array_merge([
            'name' => 'Мария Иванова',
            'email' => 'maria'.uniqid().'@example.com',
            'token' => ReaderIdentity::newToken(),
            'consent_at' => now(),
            'consent_version' => 'test',
        ], $attributes));
    }

    protected function storyUrl(Post $post): string
    {
        return route('blog.show', [$post->category, $post]);
    }
}
