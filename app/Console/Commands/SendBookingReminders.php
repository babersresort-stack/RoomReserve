<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Notifications\BookingReminderNotification;
use Illuminate\Console\Command;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';

    protected $description = 'Send booking reminder emails 24 hours before check-in';

    public function handle(): int
    {
        $windowStart = now()->addHours(24)->startOfMinute();
        $windowEnd = $windowStart->copy()->addHour();

        $bookings = Booking::query()
            ->with(['user', 'room'])
            ->whereIn('status', Booking::ACTIVE_STATUSES)
            ->whereNull('reminder_sent_at')
            ->whereBetween('check_in_at', [$windowStart, $windowEnd])
            ->get();

        foreach ($bookings as $booking) {
            if (! $booking instanceof Booking || ! $booking->user) {
                continue;
            }

            $booking->user->notify(new BookingReminderNotification($booking));
            $booking->update(['reminder_sent_at' => now()]);
        }

        $this->info('Booking reminders sent: '.$bookings->count());

        return self::SUCCESS;
    }
}
