<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Redirect;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Regenerates blog category / post / event slugs with proper Bulgarian
 * transliteration (Str::slug(..., 'bg')), records a 301 for every changed URL
 * in the `redirects` table, and collapses redirect chains on re-run.
 *
 *   php artisan content:reslug --dry-run   # preview only
 *   php artisan content:reslug             # apply (asks for confirmation)
 */
class ReslugContent extends Command
{
    protected $signature = 'content:reslug {--dry-run : Show what would change without writing}';

    protected $description = 'Rebuild blog/event slugs with bg transliteration and register 301 redirects';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        // Current slug maps (id => slug); mutated as we assign new ones so
        // uniqueSlug() sees the running state, not just the DB snapshot.
        $catSlugs = PostCategory::pluck('slug', 'id')->all();
        $postSlugs = Post::pluck('slug', 'id')->all();
        $eventSlugs = Event::pluck('slug', 'id')->all();

        $changes = [];            // [type, id, title, oldPath, newPath, newSlug]
        $catNewSlug = [];         // categoryId => newSlug (for post path building)

        // --- Categories ---
        foreach (PostCategory::orderBy('id')->get() as $cat) {
            $base = Str::slug((string) $cat->name, '-', 'bg');
            if ($base === '') {
                $this->warn("category #{$cat->id} \"{$cat->name}\": produces an empty slug — skipped");
                $catNewSlug[$cat->id] = $cat->slug;
                continue;
            }
            $new = $this->uniqueSlug($base, $catSlugs, $cat->id);
            $catSlugs[$cat->id] = $new;
            $catNewSlug[$cat->id] = $new;
            if ($new !== $cat->slug) {
                $changes[] = ['category', $cat->id, $cat->name, "/blog/{$cat->slug}", "/blog/{$new}", $new];
            }
        }

        // --- Posts (drafts included — they have live URLs too) ---
        foreach (Post::with('category')->orderBy('id')->get() as $post) {
            if (! $post->category) {
                continue;
            }
            $base = Str::slug((string) $post->title, '-', 'bg');
            if ($base === '') {
                $this->warn("post #{$post->id} \"{$post->title}\": produces an empty slug — skipped");
                continue;
            }
            $new = $this->uniqueSlug($base, $postSlugs, $post->id);
            $postSlugs[$post->id] = $new;

            $oldPath = "/blog/{$post->category->slug}/{$post->slug}";
            $newCat = $catNewSlug[$post->category_id] ?? $post->category->slug;
            $newPath = "/blog/{$newCat}/{$new}";

            if ($oldPath !== $newPath) {
                $changes[] = ['post', $post->id, $post->title, $oldPath, $newPath, $new];
            }
        }

        // --- Events ---
        foreach (Event::orderBy('id')->get() as $event) {
            $base = Str::slug((string) $event->title, '-', 'bg');
            if ($base === '') {
                $this->warn("event #{$event->id} \"{$event->title}\": produces an empty slug — skipped");
                continue;
            }
            $new = $this->uniqueSlug($base, $eventSlugs, $event->id);
            $eventSlugs[$event->id] = $new;
            if ($new !== $event->slug) {
                $changes[] = ['event', $event->id, $event->title, "/events/{$event->slug}", "/events/{$new}", $new];
            }
        }

        if (empty($changes)) {
            $this->info('All slugs are already clean — nothing to do.');

            return self::SUCCESS;
        }

        $this->table(
            ['Type', 'ID', 'Old path', 'New path'],
            array_map(fn ($c) => [$c[0], $c[1], $c[3], $c[4]], $changes),
        );
        $this->line('');
        $this->info(count($changes) . ' slug(s) will change, each with a 301 redirect from the old URL.');

        if ($dry) {
            $this->comment('Dry run — nothing was written.');

            return self::SUCCESS;
        }

        if (! $this->confirm('Apply these changes now?')) {
            $this->comment('Aborted — nothing written.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($changes) {
            // Pass 1: park every changing row on a collision-proof temp slug so
            // the unique index can't trip on a transient swap (A->B while B->C).
            foreach ($changes as $c) {
                $this->modelFor($c[0])::where('id', $c[1])->update(['slug' => '__reslug_' . $c[1]]);
            }

            // Pass 2: final slugs + redirect bookkeeping.
            foreach ($changes as [$type, $id, , $oldPath, $newPath, $newSlug]) {
                $this->modelFor($type)::where('id', $id)->update(['slug' => $newSlug]);

                // Any redirect that pointed at the old path now points at the new one.
                Redirect::where('to_path', $oldPath)->update(['to_path' => $newPath]);
                // A stale row that used the new path as a source would loop — drop it.
                Redirect::where('from_path', $newPath)->delete();
                // Register / refresh old -> new.
                Redirect::updateOrCreate(['from_path' => $oldPath], ['to_path' => $newPath]);
            }
        });

        $this->info('Done. ' . count($changes) . ' slug(s) updated, redirects written.');
        $this->comment('Verify a few old URLs return 301, then submit the sitemap in Search Console.');

        return self::SUCCESS;
    }

    /** @return class-string<\Illuminate\Database\Eloquent\Model> */
    private function modelFor(string $type): string
    {
        return match ($type) {
            'category' => PostCategory::class,
            'post' => Post::class,
            'event' => Event::class,
        };
    }

    /**
     * @param  array<int, string>  $usedById
     */
    private function uniqueSlug(string $base, array $usedById, int $selfId): string
    {
        $taken = function (string $candidate) use ($usedById, $selfId): bool {
            foreach ($usedById as $id => $slug) {
                if ((int) $id !== $selfId && $slug === $candidate) {
                    return true;
                }
            }

            return false;
        };

        if (! $taken($base)) {
            return $base;
        }

        $i = 2;
        while ($taken($base . '-' . $i)) {
            $i++;
        }

        return $base . '-' . $i;
    }
}
