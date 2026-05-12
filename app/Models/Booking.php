<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'customer_id',
        'assigned_staff_id',
        'status',
        'pickup_address',
        'delivery_address',
        'pickup_scheduled_at',
        'delivery_scheduled_at',
        'total_amount',
        'payment_channel',
        'paid_at',
        'notes',
        'cancelled_at',
        'reschedule_count',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'pickup_scheduled_at' => 'datetime',
            'delivery_scheduled_at' => 'datetime',
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'reschedule_count' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function isCancelled(): bool
    {
        return $this->status === BookingStatus::Cancelled;
    }

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }

    public function paymentChannelLabel(): ?string
    {
        if (! $this->payment_channel) {
            return null;
        }

        return match ($this->payment_channel) {
            'cod' => __('Cash on Delivery'),
            'gcash' => 'GCash',
            'maya' => 'Maya',
            'bank_transfer' => __('Bank Transfer'),
            'card' => __('Card'),
            'manual' => __('Manual'),
            default => ucfirst(str_replace('_', ' ', $this->payment_channel)),
        };
    }

    public function markAsPaid(?string $paymentChannel = null): void
    {
        $this->update([
            'paid_at' => now(),
            'payment_channel' => $paymentChannel ?? 'manual',
        ]);

        $this->transactions()->create([
            'amount' => $this->total_amount,
            'type' => 'payment',
            'notes' => __('Marked as paid by admin'),
            'recorded_at' => now(),
        ]);
    }

    public function markAsUnpaid(): void
    {
        $this->update([
            'paid_at' => null,
            'payment_channel' => null,
        ]);

        $this->transactions()->where('type', 'payment')->delete();
    }
}
