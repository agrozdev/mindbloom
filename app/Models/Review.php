<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Чака одобрение',
        self::STATUS_APPROVED => 'Одобрен',
        self::STATUS_REJECTED => 'Отхвърлен',
    ];

    protected $fillable = [
        'reader_id',
        'post_id',
        'email_log_id',
        'rating',
        'answer_feeling',
        'answer_lesson',
        'can_publish',
        'status',
    ];

    protected $casts = [
        'can_publish' => 'boolean',
        'rating' => 'integer',
    ];

    public function reader(): BelongsTo
    {
        return $this->belongsTo(Reader::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function emailLog(): BelongsTo
    {
        return $this->belongsTo(EmailLog::class);
    }

    /**
     * Shown on the site: approved by the admin and allowed by the reader.
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_APPROVED)->where('can_publish', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
