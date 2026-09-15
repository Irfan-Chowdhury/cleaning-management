<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookingPendingNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Booking $booking
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'       => 'new_booking_pending',
            'title'      => "New Booking Request #{$this->booking->id}",
            'message'    => "Customer {$this->booking->customer_name} submitted a new cleaning booking request.",
            'link'       => route('bookings.edit', $this->booking->id),
            'icon'       => 'fas fa-calendar-plus',
            'booking_id' => $this->booking->id,
        ];
    }
}
