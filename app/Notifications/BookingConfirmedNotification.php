<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification
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
            'type'       => 'booking_confirmed',
            'title'      => "Booking #{$this->booking->id} Confirmed!",
            'message'    => "Customer {$this->booking->customer_name} has confirmed booking #{$this->booking->id} for {$serviceName}.",
            'link'       => route('bookings.show', $this->booking->id),
            'icon'       => 'fas fa-calendar-check',
            'booking_id' => $this->booking->id,
        ];
    }
}
