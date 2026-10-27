<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryRead extends Model
{
    public const STATUS_VISITED = 'visited';

    public const STATUS_OPENED = 'opened';

    public const STATUS_READ = 'read';

    public const STATUS_LABELS = [
        self::STATUS_VISITED => 'Посетена',
        self::STATUS_OPENED => 'Отворена',
        self::STATUS_READ => 'Прочетена',
    ];

    public $timestamps = false;

    protected $fillable = [
        'reader_id',
        'post_id',
        'status',
        'max_seconds',
        'max_scroll',
        'visits',
        'first_seen',
        'last_seen',
    ];

    protected $casts = [
        'first_seen' => 'datetime',
        'last_seen' => 'datetime',
    ];

    public function reader(): BelongsTo
    {
        return $this->belongsTo(Reader::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    /**
     * Status from the thresholds in „Настройки“. Never downgrades.
     */
    public static function statusFor(int $seconds, int $scroll): string
    {
        if ($seconds >= (int) Setting::get('read_min_seconds') && $scroll >= (int) Setting::get('read_min_scroll')) {
            return self::STATUS_READ;
        }

        if ($seconds >= (int) Setting::get('read_opened_seconds')) {
            return self::STATUS_OPENED;
        }

        return self::STATUS_VISITED;
    }
}
