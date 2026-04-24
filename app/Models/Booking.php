<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;

    public const ACTIVE_STATUSES = ['pending', 'confirmed', 'checked_in'];

    protected $fillable = [
        'reference',
        'user_id',
        'room_id',
        'check_in_at',
        'check_out_at',
        'guests',
        'status',
        'special_requests',
        'cancelled_at',
        'cancellation_reason',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'guests' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    public function scopeOverlapping(Builder $query, CarbonInterface $start, CarbonInterface $end): Builder
    {
        return $query->where('status', '!=', 'cancelled')
            ->where('check_in_at', '<', $end)
            ->where('check_out_at', '>', $start);
    }

    public static function hasConflict(int $roomId, CarbonInterface $start, CarbonInterface $end, ?int $ignoreBookingId = null): bool
    {
        return static::query()
            ->where('room_id', $roomId)
            ->when($ignoreBookingId, fn (Builder $query) => $query->whereKeyNot($ignoreBookingId))
            ->overlapping($start, $end)
            ->exists();
    }

    public function getNightsAttribute(): int
    {
        if (! $this->check_in_at || ! $this->check_out_at) {
            return 1;
        }

        return max(1, $this->check_in_at->diffInDays($this->check_out_at));
    }

    public function getBillableGuestCountAttribute(): int
    {
        // Barkada-style rooms are billed per guest; standard rooms are billed per night.
        if ($this->room && $this->room->capacity >= 10) {
            return max(1, $this->guests);
        }

        return 1;
    }

    public function getTotalBillAttribute(): float
    {
        $rate = (float) ($this->room?->base_rate ?? 0);

        return $this->nights * $rate * $this->billable_guest_count;
    }

    public function getAvailabilityLabelAttribute(): string
    {
        $isUnavailable = in_array($this->status, self::ACTIVE_STATUSES, true)
            && $this->check_out_at
            && $this->check_out_at->isFuture();

        return $isUnavailable ? 'unavailable' : 'available';
    }
}
