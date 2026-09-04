<?php

namespace App\Models;

use App\Models\Concerns\HasMetaDescription;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasMetaDescription;

    /** The long-text field to fall back to when building a meta description. */
    protected $metaBodyField = 'description';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'description',
        'image',
        'starts_at',
        'location',
        'price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('starts_at');
    }
}
