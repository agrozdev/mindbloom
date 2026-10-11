<?php

namespace App\Models;

use App\Mail\CommentApproved;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;
use Throwable;

class Comment extends Model
{
    protected $fillable = [
        'post_id',
        'name',
        'email',
        'body',
        'ip_address',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'approval_email_sent_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('approved_at');
    }

    /**
     * Only the first name is shown publicly: "Атанас Гроздев" -> "Атанас".
     */
    public function publicName(): string
    {
        return strtok(trim($this->name), " \t") ?: $this->name;
    }

    public function isApproved(): bool
    {
        return $this->approved_at !== null;
    }

    /**
     * Publishes the comment and thanks the reader by email. The email only
     * goes out on the first approval, so hiding and re-approving a comment
     * doesn't send it twice.
     */
    public function approve(): void
    {
        if ($this->isApproved()) {
            return;
        }

        $this->forceFill(['approved_at' => now()])->save();

        if ($this->approval_email_sent_at !== null) {
            return;
        }

        try {
            Mail::to($this->email)->send(new CommentApproved($this));
            $this->forceFill(['approval_email_sent_at' => now()])->save();
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function unapprove(): void
    {
        $this->forceFill(['approved_at' => null])->save();
    }
}
