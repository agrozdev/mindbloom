<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'description',
        'image',
        'starts_at',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
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
