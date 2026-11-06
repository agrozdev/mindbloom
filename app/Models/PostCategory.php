<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostCategory extends Model
{
    protected $table = 'post_categories';

    protected $fillable = [
        'name',
        'slug',
        'meta_description',
    ];

    protected static function booted(): void
    {
        // Renaming a category's slug in the admin used to leave the old URL as a 404.
        // (Its posts' old URLs are caught by the post-slug fallback in bootstrap/app.php.)
        static::updated(function (PostCategory $category) {
            if ($category->wasChanged('slug') && $category->getOriginal('slug')) {
                Redirect::record('/blog/' . $category->getOriginal('slug'), '/blog/' . $category->slug);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'category_id');
    }
}
