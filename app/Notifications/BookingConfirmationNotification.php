<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public string $headline,
        public string $description,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Room booking ' . $this->headline)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->description)
            ->line('Reference: ' . $this->booking->reference)
            ->line('Room: ' . $this->booking->room->name . ' (' . $this->booking->room->code . ')')
            ->line('Check-in: ' . $this->booking->check_in_at->format('M j, Y g:i A'))
            ->line('Check-out: ' . $this->booking->check_out_at->format('M j, Y g:i A'))
            ->line('Total bill: PHP ' . number_format($this->booking->total_bill, 2))
            ->line('Status: ' . ucfirst($this->booking->status))
            ->salutation('RoomReserve');
    }
}
