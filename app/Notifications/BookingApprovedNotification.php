<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingApprovedNotification extends Notification
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
            'type'       => 'booking_approved',
            'title'      => "Booking #{$this->booking->id} Approved!",
            'message'    => "Your booking for {$serviceName} has been approved. Click to view details and proceed to confirmation.",
            'link'       => route('customer.bookings.index'),
            'icon'       => 'fas fa-check-circle',
            'booking_id' => $this->booking->id,
        ];
    }
}
