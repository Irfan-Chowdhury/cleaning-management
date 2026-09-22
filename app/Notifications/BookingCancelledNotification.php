<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification
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
        $bookingCode = 'BK-' . sprintf('%03d', $this->booking->id);
        $customerName = $this->booking->customer_name ?: 'Customer';

        return [
            'type'       => 'booking_cancelled',
            'title'      => "Booking Schedule Cancelled #{$this->booking->id}",
            'message'    => "Customer {$customerName} cancelled booking schedule {$bookingCode}.",
            'link'       => route('bookings.edit', $this->booking->id),
            'icon'       => 'fas fa-calendar-times',
            'booking_id' => $this->booking->id,
        ];
    }
}
