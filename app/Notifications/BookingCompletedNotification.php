<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingCompletedNotification extends Notification
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
        $serviceName = $this->booking->service?->name ?? 'Cleaning Service';

        return [
            'type'       => 'booking_completed',
            'title'      => "Booking #{$this->booking->id} Completed!",
            'message'    => "Your booking for {$serviceName} has been completed. Thank you for choosing our cleaning service!",
            'link'       => route('customer.bookings.show', $this->booking->id),
            'icon'       => 'fas fa-check-double',
            'booking_id' => $this->booking->id,
        ];
    }
}
