<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmailLog extends Model
{
    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    public $timestamps = false;

    protected $fillable = [
        'reader_id',
        'post_id',
        'template_id',
        'subject',
        'body',
        'feedback_token',
        'sent_by',
        'status',
        'error',
        'sent_at',
    ];

    protected $hidden = ['feedback_token'];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function reader(): BelongsTo
    {
        return $this->belongsTo(Reader::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
