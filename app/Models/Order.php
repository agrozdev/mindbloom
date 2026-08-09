<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Order extends Model
{
    const STATUS_PENDING = 'pending';

    const STATUS_PAID = 'paid';

    const STATUS_FAILED = 'failed';

    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'orderable_type',
        'orderable_id',
        'name',
        'email',
        'phone',
        'amount',
        'currency',
        'status',
        'transaction_id',
        'notify_payload',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'notify_payload' => 'array',
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->uuid ??= (string) Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function orderable(): MorphTo
    {
        return $this->morphTo();
    }
}
