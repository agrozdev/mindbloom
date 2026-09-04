<?php

namespace App\Models;

use App\Models\Concerns\HasMetaDescription;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasMetaDescription;

    /** The long-text field to fall back to when building a meta description. */
    protected $metaBodyField = 'description';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'description',
        'icon',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
