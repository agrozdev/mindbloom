<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reader extends Model
{
    protected $fillable = [
        'name',
        'email',
        'token',
        'consent_at',
        'consent_version',
        'consent_ip',
        'user_agent',
        'signup_story_id',
        'unsubscribed_at',
    ];

    protected $hidden = ['token'];

    protected $casts = [
        'consent_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    public static function normalizeEmail(string $email): string
    {
        return mb_strtolower(trim($email));
    }

    public function storyReads(): HasMany
    {
        return $this->hasMany(StoryRead::class);
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    public function lastSentEmail(): HasOne
    {
        return $this->hasOne(EmailLog::class)->ofMany(['sent_at' => 'max'], fn ($query) => $query->where('status', EmailLog::STATUS_SENT));
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function signupStory(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'signup_story_id');
    }

    /**
     * Only the first name, for greetings and published reviews.
     */
    public function firstName(): string
    {
        return strtok(trim($this->name), " \t") ?: $this->name;
    }

    public function isUnsubscribed(): bool
    {
        return $this->unsubscribed_at !== null;
    }

    public function canBeEmailed(): bool
    {
        return ! $this->isUnsubscribed() && $this->consent_at !== null;
    }

    /**
     * Why „Изпрати имейл“ is disabled, or null when it isn't.
     */
    public function emailBlockReason(): ?string
    {
        return match (true) {
            $this->isUnsubscribed() => 'Читателят се е отписал.',
            $this->consent_at === null => 'Няма съгласие за получаване на имейли.',
            default => null,
        };
    }

    public function wasEmailedRecently(int $days = 7): bool
    {
        return $this->emailLogs()
            ->where('status', EmailLog::STATUS_SENT)
            ->where('sent_at', '>=', now()->subDays($days))
            ->exists();
    }
}
