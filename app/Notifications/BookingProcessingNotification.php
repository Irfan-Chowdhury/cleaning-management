<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingProcessingNotification extends Notification
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
            'type'       => 'booking_processing',
            'title'      => "Booking #{$this->booking->id} Cleaner Assigned!",
            'message'    => "Your {$serviceName} booking has been assigned and is being prepared for service.",
            'link'       => route('customer.bookings.show', $this->booking->id),
            'icon'       => 'fas fa-spinner',
            'booking_id' => $this->booking->id,
        ];
    }
}
